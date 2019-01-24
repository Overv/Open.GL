// Link statically with GLEW
#define GLEW_STATIC

// Headers
#include <GL/glew.h>
#include <SFML/Window.hpp>

// Vertex shader
const GLchar* vertexShaderSrc = R"glsl(
    #version 150 core

    in vec2 pos;

    void main()
    {
        gl_Position = vec4(pos, 0.0, 1.0);
    }
)glsl";

// Geometry shader
const GLchar* geometryShaderSrc = R"glsl(
    #version 150 core

    layout(points) in;
    layout(triangle_strip, max_vertices = 5) out;

    void main()
    {
        gl_Position = gl_in[0].gl_Position + vec4(-0.1, 0.1, 0.0, 0.0);
        EmitVertex();

        gl_Position = gl_in[0].gl_Position + vec4(0.1, 0.1, 0.0, 0.0);
        EmitVertex();

        gl_Position = gl_in[0].gl_Position + vec4(0.1, -0.1, 0.0, 0.0);
        EmitVertex();

        gl_Position = gl_in[0].gl_Position + vec4(-0.1, -0.1, 0.0, 0.0);
        EmitVertex();

        gl_Position = gl_in[0].gl_Position + vec4(-0.1, 0.1, 0.0, 0.0);
        EmitVertex();

        EndPrimitive();
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

// Shader creation helper
GLuint createShader(GLenum type, const GLchar* src) {
    GLuint shader = glCreateShader(type);
    glShaderSource(shader, 1, &src, nullptr);
    glCompileShader(shader);
    return shader;
}

int main()
{
    sf::ContextSettings settings;
    settings.depthBits = 24;
    settings.stencilBits = 8;
    settings.majorVersion = 3;
    settings.minorVersion = 2;

    sf::Window window(sf::VideoMode(800, 600, 32), "Circles", sf::Style::Titlebar | sf::Style::Close, settings);

    // Initialize GLEW
    glewExperimental = GL_TRUE;
    glewInit();

    // Compile and activate shaders
    GLuint vertexShader = createShader(GL_VERTEX_SHADER, vertexShaderSrc);
    GLuint geometryShader = createShader(GL_GEOMETRY_SHADER, geometryShaderSrc);
    GLuint fragmentShader = createShader(GL_FRAGMENT_SHADER, fragmentShaderSrc);

    GLuint shaderProgram = glCreateProgram();
    glAttachShader(shaderProgram, vertexShader);
    glAttachShader(shaderProgram, geometryShader);
    glAttachShader(shaderProgram, fragmentShader);
    glLinkProgram(shaderProgram);
    glUseProgram(shaderProgram);

    // Create VBO with point coordinates
    GLuint vbo;
    glGenBuffers(1, &vbo);

    GLfloat points[] = {
        -0.45f,  0.45f,
         0.45f,  0.45f,
         0.45f, -0.45f,
        -0.45f, -0.45f,
    };

    glBindBuffer(GL_ARRAY_BUFFER, vbo);
    glBufferData(GL_ARRAY_BUFFER, sizeof(points), points, GL_STATIC_DRAW);

    // Create VAO
    GLuint vao;
    glGenVertexArrays(1, &vao);
    glBindVertexArray(vao);

    // Specify the layout of the vertex data
    GLint posAttrib = glGetAttribLocation(shaderProgram, "pos");
    glEnableVertexAttribArray(posAttrib);
    glVertexAttribPointer(posAttrib, 2, GL_FLOAT, GL_FALSE, 0, 0);

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

        // Render frame
        glDrawArrays(GL_POINTS, 0, 4);

        // Swap buffers
        window.display();
    }

    glDeleteProgram(shaderProgram);
    glDeleteShader(fragmentShader);
    glDeleteShader(geometryShader);
    glDeleteShader(vertexShader);

    glDeleteBuffers(1, &vbo);

    glDeleteVertexArrays(1, &vao);

    window.close();

    return 0;
}
