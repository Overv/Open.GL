Window and OpenGL context
========

Before you can start drawing things, you need to initialize OpenGL. This is done by creating an OpenGL context, which is essentially a state machine that stores all data related to the rendering of your application. When your application closes, the OpenGL context is destroyed and everything is cleaned up.

The problem is that creating a window and an OpenGL context is not part of the OpenGL specification. That means it is done differently on every platform out there! Developing applications using OpenGL is all about being portable, so this is the last thing we need. Luckily there are libraries out there that abstract this process, so that you can maintain the same codebase for all supported platforms.

While the available libraries out there all have advantages and disadvantages, they do all have a certain program flow in common. You start by specifying the properties of the game window, such as the title and the size and the properties of the OpenGL context, like the [anti-aliasing](http://en.wikipedia.org/wiki/Anti-aliasing) level. Your application will then initiate the event loop, which contains an important set of tasks that need to be completed over and over again until the window closes. These tasks usually handle window events like mouse clicks, updating the rendering state and then drawing.

This program flow would look something like this in pseudocode:

	#include <libraryheaders>

	int main()
	{
		createWindow(title, width, height);
		createOpenGLContext(settings);

		while (windowOpen)
		{
			while (event = newEvent())
				handleEvent(event);

			updateScene();

			drawGraphics();
			presentGraphics();
		}

		return 0;
	}

When rendering a frame, the results will be stored in an offscreen buffer known as the *back buffer* to make sure the user only sees the final result. The `presentGraphics()` call will copy the result from the back buffer to the visible window buffer, the *front buffer*. Every application that makes use of real-time graphics will have a program flow that comes down to this, whether it uses a library or native code.

Supporting resizable windows with OpenGL introduces some complexities as resources need to be reloaded and buffers need to be recreated to fit the new window size. It's more convenient for the learning process to not bother with such details yet, so we'll only deal with fixed size (fullscreen) windows for now.

Setup
=====

>Instead of reading this chapter, you can make use of the [OpenGL quickstart boilerplate](https://github.com/Polytonic/Glitter), which makes setting up an OpenGL project with all of the required libraries very easy. You'll just have to install SOIL separately.

The first thing to do when starting a new OpenGL project is to dynamically link with OpenGL.

- **Windows**: Add `opengl32.lib` to your linker input
- **Linux**: Include `-lGL` in your compiler options
- **OS X**: Add `-framework OpenGL` to your compiler options

<blockquote class="important">Make sure that you do <strong>not</strong> include <code>opengl32.dll</code> with your application. This file is already included with Windows and may differ per version, which will cause problems on other computers.</blockquote>

The rest of the steps depend on which library you choose to use for creating the window and context.

Libraries
========

There are many libraries around that can create a window and an accompanying OpenGL context for you. There is no best library out there, because everyone has different needs and ideals. I've chosen to discuss the process for the three most popular libraries here for completeness, but you can find more detailed guides on their respective websites. All code after this chapter will be independent of your choice of library here.

[SFML](#SFML)
--------

SFML is a cross-platform C++ multimedia library that provides access to graphics, input, audio, networking and the system. The downside of using this library is that it tries hard to be an all-in-one solution. You have little to no control over the creation of the OpenGL context, as it was designed to be used with its own set of drawing functions.

[SDL](#SDL)
--------

SDL is also a cross-platform multimedia library, but targeted at C. That makes it a bit rougher to use for C++ programmers, but it's an excellent alternative to SFML. It supports more exotic platforms and most importantly, offers more control over the creation of the OpenGL context than SFML.

[GLFW](#GLFW)
--------

GLFW, as the name implies, is a C library specifically designed for use with OpenGL. Unlike SDL and SFML it only comes with the absolute necessities: window and context creation and input management. It offers the most control over the OpenGL context creation out of these three libraries.

Others
--------

There are a few other options, like [freeglut](http://freeglut.sourceforge.net/) and [OpenGLUT](http://openglut.sourceforge.net/), but I personally think the aforementioned libraries are vastly superior in control, ease of use and on top of that more up-to-date.

SFML
========

The OpenGL context is created implicitly when opening a new window in SFML, so that's all you have to do. SFML also comes with a graphics package, but since we're going to use OpenGL directly, we don't need it.

Building
--------

After you've downloaded the SFML binaries package or compiled it yourself, you'll find the needed files in the `lib` and `include` folders.

- Add the `lib` folder to your library path and link with `sfml-system` and `sfml-window`. With Visual Studio on Windows, link with the `sfml-system-s` and `sfml-window-s` files in `lib/vc2008` instead.
- Add the `include` folder to your include path.

> The SFML libraries have a simple naming convention for different configurations. If you want to dynamically link, simply remove the `-s` from the name, define `SFML_DYNAMIC` and copy the shared libraries. If you want to use the binaries with debug symbols, additionally append `-d` to the name.

To verify that you've done this correctly, try compiling and running the following code:

	#include <SFML/System.hpp>

	int main()
	{
		sf::sleep(sf::seconds(1.f));
		return 0;
	}

It should show a console application and exit after a second. If you run into any trouble, you can find more detailed information for [Visual Studio](http://sfml-dev.org/tutorials/2.1/start-vc.php), [Code::Blocks](http://sfml-dev.org/tutorials/2.1/start-cb.php) and [gcc](http://sfml-dev.org/tutorials/2.1/start-linux.php) in the tutorials on the SFML website.

Code
--------

Start by including the window package and defining the entry point of your application.

	#include <SFML/Window.hpp>

	int main()
	{
		return 0;
	}

A window can be opened by creating a new instance of `sf::Window`. The basic constructor takes an `sf::VideoMode` structure, a title for the window and a window style. The `sf::VideoMode` structure specifies the width, height and optionally the pixel depth of the window. Finally, the requirement for a fixed size window is specified by overriding the default style of `Style::Resize|Style::Close`. It is also possible to create a fullscreen window by passing `Style::Fullscreen` as window style.

    sf::ContextSettings settings;
    settings.depthBits = 24;
    settings.stencilBits = 8;
    settings.antialiasingLevel = 2; // Optional
    // Request OpenGL version 3.2
    settings.majorVersion = 3;
    settings.minorVersion = 2;
    settings.attributeFlags = sf::ContextSettings::Core;

	sf::Window window(sf::VideoMode(800, 600), "OpenGL", sf::Style::Close, settings);

The constructor can also take an `sf::ContextSettings` structure that allows you to request an OpenGL context and specify the anti-aliasing level and the accuracy of the depth and stencil buffers. The latter two will be discussed later, so you don't have to worry about these yet. In the latest version of SFML, you do need to request these manually with the code above. We request an OpenGL context of version 3.2 in the core profile as opposed to the compatibility mode which is default. Using the default compatibility mode may cause problems while using modern OpenGL on some systems, thus we use the core profile.

Note that these settings are only a hint, SFML will try to find the closest valid match. It will, for example, likely create a context with a newer OpenGL version than we specified.

When running this, you'll notice that the application instantly closes after creating the window. Let's add the event loop to deal with that.

	bool running = true;
	while (running)
	{
		sf::Event windowEvent;
		while (window.pollEvent(windowEvent))
		{

		}
	}

When something happens to your window, an event is posted to the event queue. There is a wide variety of events, including window size changes, mouse movement and key presses. It's up to you to decide which events require additional action, but there is at least one that needs to be handled to make your application run well.

	switch (windowEvent.type)
	{
	case sf::Event::Closed:
		running = false;
		break;
	}

When the user attempts to close the window, the `Closed` event is fired and we act on that by exiting the application. Try removing that line and you'll see that it's impossible to close the window by normal means. If you prefer a fullscreen window, you should add the escape key as a means to close the window:

	case sf::Event::KeyPressed:
		if (windowEvent.key.code == sf::Keyboard::Escape)
			running = false;
		break;

You have your window and the important events are acted upon, so you're now ready to put something on the screen. After drawing something, you can swap the back buffer and the front buffer with `window.display()`.

When you run your application, you should see something like this:

<img src="/media/img/c1_window.png" alt="" />

Note that SFML allows you to have multiple windows. If you want to make use of this feature, make sure to call `window.setActive()` to activate a certain window for drawing operations.

Now that you have a window and a context, there's [one more thing](#Onemorething) that needs to be done.

SDL
========

SDL comes with many different modules, but for creating a window with an accompanying OpenGL context we're only interested in the video module. It will take care of everything we need, so let's see how to use it.

Building
--------

After you've downloaded the SDL binaries or compiled them yourself, you'll find the needed files in the `lib` and `include` folders.

- Add the `lib` folder to your library path and link with `SDL2` and `SDL2main`.
- SDL uses dynamic linking, so make sure that the shared library (`SDL2.dll`, `SDL2.so`) is with your executable.
- Add the `include` folder to your include path.

To verify that you're ready, try compiling and running the following snippet of code:

	#include <SDL.h>

	int main(int argc, char *argv[])
	{
		SDL_Init(SDL_INIT_EVERYTHING);

		SDL_Delay(1000);

		SDL_Quit();
		return 0;
	}

It should show a console application and exit after a second. If you run into any trouble, you can find more [detailed information](http://wiki.libsdl.org/FrontPage) for all kinds of platforms and compilers in the tutorials on the web.

Code
--------

Start by defining the entry point of your application and include the headers for SDL.

	#include <SDL.h>
	#include <SDL_opengl.h>

	int main(int argc, char *argv[])
	{
		return 0;
	}

To use SDL in an application, you need to tell SDL which modules you need and when to unload them. You can do this with two lines of code.

	SDL_Init(SDL_INIT_VIDEO);
	...
	SDL_Quit();
	return 0;

The `SDL_Init` function takes a bitfield with the modules to load. The video module includes everything you need to create a window and an OpenGL context.

Before doing anything else, first tell SDL that you want a forward compatible OpenGL 3.2 context:

	SDL_GL_SetAttribute(SDL_GL_CONTEXT_PROFILE_MASK, SDL_GL_CONTEXT_PROFILE_CORE);
	SDL_GL_SetAttribute(SDL_GL_CONTEXT_MAJOR_VERSION, 3);
	SDL_GL_SetAttribute(SDL_GL_CONTEXT_MINOR_VERSION, 2);
	SDL_GL_SetAttribute(SDL_GL_STENCIL_SIZE, 8);

You also need to tell SDL to create a stencil buffer, which will be relevant for a later chapter. After that, create a window using the `SDL_CreateWindow` function.

	SDL_Window* window = SDL_CreateWindow("OpenGL", 100, 100, 800, 600, SDL_WINDOW_OPENGL);

The first argument specifies the title of the window, the next two are the X and Y position and the two after those are the width and height. If the position doesn't matter, you can specify `SDL_WINDOWPOS_UNDEFINED` or `SDL_WINDOWPOS_CENTERED` for the second and third argument. The final parameter specifies window properties like:

- *SDL_WINDOW_OPENGL* - Create a window ready for OpenGL.
- *SDL_WINDOW_RESIZABLE* - Create a resizable window.
- **Optional** *SDL_WINDOW_FULLSCREEN* - Create a fullscreen window.

After you've created the window, you can create the OpenGL context:

	SDL_GLContext context = SDL_GL_CreateContext(window);
	...
	SDL_GL_DeleteContext(context);

The context should be destroyed right before calling `SDL_Quit()` to clean up the resources.

Then comes the most important part of the program, the event loop:

	SDL_Event windowEvent;
	while (true)
	{
		if (SDL_PollEvent(&windowEvent))
		{
			if (windowEvent.type == SDL_QUIT) break;
		}

		SDL_GL_SwapWindow(window);
	}

The `SDL_PollEvent` function will check if there are any new events that have to be handled. An event can be anything from a mouse click to the user moving the window. Right now, the only event you need to respond to is the user pressing the little X button in the corner of the window. By breaking from the main loop, `SDL_Quit` is called and the window and graphics surface are destroyed. `SDL_GL_SwapWindow` here takes care of swapping the front and back buffer after new things have been drawn by your application.

If you have a fullscreen window, it would be preferable to use the escape key as a means to close the window.

	if (windowEvent.type == SDL_KEYUP &&
		windowEvent.key.keysym.sym == SDLK_ESCAPE) break;

When you run your application now, you should see something like this:

<img src="/media/img/c1_window.png" alt="" />

Now that you have a window and a context, there's [one more thing](#Onemorething) that needs to be done.

GLFW
========

GLFW is tailored specifically for using OpenGL, so it is by far the easiest to use for our purpose.

Building
--------

After you've downloaded the GLFW binaries package from the website or compiled the library yourself, you'll find the headers in the `include` folder and the libraries for your compiler in one of the `lib` folders.

- Add the appropriate `lib` folder to your library path and link with `GLFW`.
- Add the `include` folder to your include path.

> You can also dynamically link with GLFW if you want to. Simply link with `GLFWDLL` and include the shared library with your executable.

Here is a simple snippet of code to check your build configuration:

	#include <GLFW/glfw3.h>
	#include <thread>

	int main()
	{
	    glfwInit();
		std::this_thread::sleep_for(std::chrono::seconds(1));
	    glfwTerminate();
	}

It should show a console application and exit after a second. If you run into any trouble, just ask in the comments and you'll receive help.

Code
--------

Start by simply including the GLFW header and define the entry point of the application.

	#include <GLFW/glfw3.h>

	int main()
	{
		return 0;
	}

To use GLFW, it needs to be initialised when the program starts and you need to give it a chance to clean up when your program closes. The `glfwInit` and `glfwTerminate` functions are geared towards that purpose.

	glfwInit();
	...
	glfwTerminate();

The next thing to do is creating and configuring the window. Before calling `glfwCreateWindow`, we first set some options.

	glfwWindowHint(GLFW_CONTEXT_VERSION_MAJOR, 3);
	glfwWindowHint(GLFW_CONTEXT_VERSION_MINOR, 2);
	glfwWindowHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);
	glfwWindowHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);

	glfwWindowHint(GLFW_RESIZABLE, GL_FALSE);

	GLFWwindow* window = glfwCreateWindow(800, 600, "OpenGL", nullptr, nullptr); // Windowed
	GLFWwindow* window =
	    glfwCreateWindow(800, 600, "OpenGL", glfwGetPrimaryMonitor(), nullptr); // Fullscreen

You'll immediately notice the first three lines of code that are only relevant for this library. It is specified that we require the OpenGL context to support OpenGL 3.2 at the least. The `GLFW_OPENGL_PROFILE` option specifies that we want a context that only supports the new core functionality.

The first two parameters of glfwCreateWindow specify the width and height of the drawing surface and the third parameter specifies the window title. The fourth parameter should be set to `NULL` for windowed mode and `glfwGetPrimaryMonitor()` for fullscreen mode. The last parameter allows you to specify an existing OpenGL context to share resources like textures with. The `glfwWindowHint` function is used to specify additional requirements for a window.

After creating the window, the OpenGL context has to be made active:

	glfwMakeContextCurrent(window);

Next comes the event loop, which in the case of GLFW works a little differently than the other libraries. GLFW uses a so-called *closed* event loop, which means you only have to handle events when you need to. That means your event loop will look really simple:

	while(!glfwWindowShouldClose(window))
	{
		glfwSwapBuffers(window);
		glfwPollEvents();
	}

The only required functions in the loop are `glfwSwapBuffers` to swap the back buffer and front buffer after you've finished drawing and `glfwPollEvents` to retrieve window events. If you are making a fullscreen application, you should handle the escape key to easily return to the desktop.

	if (glfwGetKey(window, GLFW_KEY_ESCAPE) == GLFW_PRESS)
		glfwSetWindowShouldClose(window, GL_TRUE);

If you want to learn more about handling input, you can refer to the [documentation](http://www.glfw.org/docs/3.0/group__input.html).

<img src="/media/img/c1_window.png" alt="" />

You should now have a window or a full screen surface with an OpenGL context. Before you can start drawing stuff however, there's [one more thing](#Onemorething) that needs to be done.

One more thing
========

Unfortunately, we can't just call the functions we need yet. This is because it's the duty of the graphics card vendor to implement OpenGL functionality in their drivers based on what the graphics card supports. You wouldn't want your program to only be compatible with a single driver version and graphics card, so we'll have to do something clever.

Your program needs to check which functions are available at runtime and link with them dynamically. This is done by finding the addresses of the functions, assigning them to function pointers and calling them. That looks something like this:

<blockquote class="important">Don't try to run this code, it's just for demonstration purposes.</blockquote>

	// Specify prototype of function
	typedef void (*GENBUFFERS) (GLsizei, GLuint*);

	// Load address of function and assign it to a function pointer
	GENBUFFERS glGenBuffers = (GENBUFFERS)wglGetProcAddress("glGenBuffers");
	// or Linux:
	GENBUFFERS glGenBuffers = (GENBUFFERS)glXGetProcAddress((const GLubyte *) "glGenBuffers");
	// or OSX:
	GENBUFFERS glGenBuffers = (GENBUFFERS)NSGLGetProcAddress("glGenBuffers");

	// Call function as normal
	GLuint buffer;
	glGenBuffers(1, &buffer);

Let me begin by asserting that it is perfectly normal to be scared by this snippet of code. You may not be familiar with the concept of function pointers yet, but at least try to roughly understand what is happening here. You can imagine that going through this process of defining prototypes and finding addresses of functions is very tedious and in the end nothing more than a complete waste of time.

The good news is that there are libraries that have solved this problem for us. The most popular and best maintained library right now is *GLEW* and there's no reason for that to change anytime soon. Nevertheless, the alternative library *GLEE* works almost completely the same save for the initialization and cleanup code.

If you haven't built GLEW yet, do so now. We'll now add GLEW to your project.

* Start by linking your project with the static GLEW library in the `lib` folder. This is either `glew32s.lib` or `GLEW` depending on your platform.
* Add the `include` folder to your include path.

Now just include the header in your program, but make sure that it is included before the OpenGL headers or the library you used to create your window.

	#define GLEW_STATIC
	#include <GL/glew.h>

Don't forget to define `GLEW_STATIC` either using this preprocessor directive or by adding the `-DGLEW_STATIC` directive to your compiler command-line parameters or project settings.

> If you prefer to dynamically link with GLEW, leave out the define and link with `glew32.lib` instead of `glew32s.lib` on Windows. Don't forget to include `glew32.dll` or `libGLEW.so` with your executable!

Now all that's left is calling `glewInit()` after the creation of your window and OpenGL context. The `glewExperimental` line is necessary to force GLEW to use a modern OpenGL method for checking if a function is available.

	glewExperimental = GL_TRUE;
	glewInit();

Make sure that you've set up your project correctly by calling the `glGenBuffers` function, which was loaded by GLEW for you!

	GLuint vertexBuffer;
	glGenBuffers(1, &vertexBuffer);

	printf("%u\n", vertexBuffer);

Your program should compile and run without issues and display the number `1` in your console. If you need more help with using GLEW, you can refer to the [website](http://glew.sourceforge.net/install.html) or ask in the comments.

Now that we're past all of the configuration and initialization work, I'd advise you to make a copy of your current project so that you won't have to write all of the boilerplate code again when starting a new project.

Now, let's get to [drawing things](/drawing)!
