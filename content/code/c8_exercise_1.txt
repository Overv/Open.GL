// Link statically with GLEW
#define GLEW_STATIC

// Headers
#include <GL/glew.h>
#include <SFML/Window.hpp>
#include <chrono>

// Vertex shader
const GLchar* vertexShaderSrc = R"glsl(
    #version 150 core

    in vec2 position;
    in vec2 velocity;
    in vec2 originalPos;

    out vec2 outPosition;
    out vec2 outVelocity;

    uniform float time;
    uniform vec2 mousePos;

    void main()
    {
        // Points move towards their original position...
        vec2 newVelocity = originalPos - position;

        // ... unless the mouse is nearby. In that case, they move towards the mouse.
        if (length(mousePos - originalPos) < 0.75f) {
            vec2 acceleration = 1.5f * normalize(mousePos - position);
            newVelocity = velocity + acceleration * time;
        }
        
        if (length(newVelocity) > 1.0f)
            newVelocity = normalize(newVelocity);

        vec2 newPosition = position + newVelocity * time;
        outPosition = newPosition;
        outVelocity = newVelocity;
        gl_Position = vec4(newPosition, 0.0, 1.0);
    }
)glsl";

// Fragment shader
const GLchar* fragmentShaderSrc = R"glsl(
    #version 150 core

    out vec4 outColor;

    void main()
    {
        outColor = vec4(1.0, 0.0, 0.0, 1.0);
    }
)glsl";

int main()
{
    sf::ContextSettings settings;
    settings.depthBits = 24;
    settings.stencilBits = 8;
    settings.majorVersion = 3;
    settings.minorVersion = 2;

    sf::Window window(sf::VideoMode(800, 800, 32), "Transform Feedback", sf::Style::Titlebar | sf::Style::Close, settings);

    // Initialize GLEW
    glewExperimental = GL_TRUE;
    glewInit();

    // Compile shaders
    GLuint vertexShader = glCreateShader(GL_VERTEX_SHADER);
    glShaderSource(vertexShader, 1, &vertexShaderSrc, nullptr);
    glCompileShader(vertexShader);

    GLuint fragmentShader = glCreateShader(GL_FRAGMENT_SHADER);
    glShaderSource(fragmentShader, 1, &fragmentShaderSrc, nullptr);
    glCompileShader(fragmentShader);

    // Create program and specify transform feedback variables
    GLuint program = glCreateProgram();
    glAttachShader(program, vertexShader);
    glAttachShader(program, fragmentShader);

    const GLchar* feedbackVaryings[] = { "outPosition", "outVelocity" };
    glTransformFeedbackVaryings(program, 2, feedbackVaryings, GL_INTERLEAVED_ATTRIBS);

    glLinkProgram(program);
    glUseProgram(program);

    GLint uniTime = glGetUniformLocation(program, "time");
    GLint uniMousePos = glGetUniformLocation(program, "mousePos");

    // Create VAO
    GLuint vao;
    glGenVertexArrays(1, &vao);
    glBindVertexArray(vao);

    // Create input VBO and vertex format
    GLfloat data[600] = {};
    // Vertex format: 6 floats per vertex:
    // pos.x  pox.y  vel.x  vel.y  origPos.x  origPos.y

    // Set original and initial positions
    for (int y = 0; y < 10; y++) {
        for (int x = 0; x < 10; x++) {
            data[60 * y + 6 * x] = 0.2f * x - 0.9f;
            data[60 * y + 6 * x + 1] = 0.2f * y - 0.9f;
            data[60 * y + 6 * x + 4] = 0.2f * x - 0.9f;
            data[60 * y + 6 * x + 5] = 0.2f * y - 0.9f;
        }
    }

    GLuint vbo;
    glGenBuffers(1, &vbo);
    glBindBuffer(GL_ARRAY_BUFFER, vbo);
    glBufferData(GL_ARRAY_BUFFER, sizeof(data), data, GL_STREAM_DRAW);

    GLint posAttrib = glGetAttribLocation(program, "position");
    glEnableVertexAttribArray(posAttrib);
    glVertexAttribPointer(posAttrib, 2, GL_FLOAT, GL_FALSE, 6 * sizeof(GLfloat), 0);

    GLint velAttrib = glGetAttribLocation(program, "velocity");
    glEnableVertexAttribArray(velAttrib);
    glVertexAttribPointer(velAttrib, 2, GL_FLOAT, GL_FALSE, 6 * sizeof(GLfloat), (void*)(2 * sizeof(GLfloat)));

    GLint origPosAttrib = glGetAttribLocation(program, "originalPos");
    glEnableVertexAttribArray(origPosAttrib);
    glVertexAttribPointer(origPosAttrib, 2, GL_FLOAT, GL_FALSE, 6 * sizeof(GLfloat), (void*)(4 * sizeof(GLfloat)));

    // Create transform feedback buffer
    GLuint tbo;
    glGenBuffers(1, &tbo);
    glBindBuffer(GL_ARRAY_BUFFER, tbo);
    glBufferData(GL_ARRAY_BUFFER, 400 * sizeof(GLfloat), nullptr, GL_STATIC_READ);

    glBindBufferBase(GL_TRANSFORM_FEEDBACK_BUFFER, 0, tbo);
    GLfloat feedback[400];

    glBindBuffer(GL_ARRAY_BUFFER, vbo);

    glPointSize(5.0f);

    auto t_prev = std::chrono::high_resolution_clock::now();

    bool running = true;
    while (running)
    {
        sf::Event windowEvent;
        while (window.pollEvent(windowEvent))
        {
            switch (windowEvent.type)
            {
            case sf::Event::Closed:
                running = false;
                break;
            }
        }

        // Clear the screen to black
        glClearColor(0.0f, 0.0f, 0.0f, 1.0f);
        glClear(GL_COLOR_BUFFER_BIT);

        // Calculate delta time
        auto t_now = std::chrono::high_resolution_clock::now();
        float time = std::chrono::duration_cast<std::chrono::duration<float>>(t_now - t_prev).count();
        t_prev = t_now;
        glUniform1f(uniTime, time);

        // Update mouse position
        glUniform2f(uniMousePos, sf::Mouse::getPosition(window).x / 400.0f - 1, -sf::Mouse::getPosition(window).y / 400.0f + 1);

        // Perform feedback transform and draw vertices
        glBeginTransformFeedback(GL_POINTS);
            glDrawArrays(GL_POINTS, 0, 100);
        glEndTransformFeedback();

        // Swap buffers
        window.display();
        
        // Update vertices' position and velocity using transform feedback
        glGetBufferSubData(GL_TRANSFORM_FEEDBACK_BUFFER, 0, sizeof(feedback), feedback);

        for (int i = 0; i < 100; i++) {
            data[6 * i] = feedback[4 * i];
            data[6 * i + 1] = feedback[4 * i + 1];
            data[6 * i + 2] = feedback[4 * i + 2];
            data[6 * i + 3] = feedback[4 * i + 3];
        }

        // glBufferData() would reallocate the whole vertex data buffer, which is unnecessary here.
        // glBufferSubData() is used instead - it updates an existing buffer.
        glBufferSubData(GL_ARRAY_BUFFER, 0, sizeof(data), data);
    }

    glDeleteProgram(program);
    glDeleteShader(fragmentShader);
    glDeleteShader(vertexShader);

    glDeleteBuffers(1, &tbo);
    glDeleteBuffers(1, &vbo);

    glDeleteVertexArrays(1, &vao);

    window.close();

    return 0;
}
