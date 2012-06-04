Window and OpenGL context
========

Before you can start drawing things, you need to initialize OpenGL. This is done by creating an OpenGL context, which is essentially a state machine that stores all data related to the rendering of your application. When your application closes, the OpenGL context is destroyed and everything is cleaned up.

The problem is that creating a window and an OpenGL context is not part of the OpenGL specification. That means it is done differently on every platform out there! Developing applications using OpenGL is all about being portable, so this is the last thing we need. Luckily there are libraries out there that abstract this process, so that you can maintain the same codebase for all supported platforms.

While the available libraries out there all have advantages and disadvantages, they do all have a certain program flow in common. You start by specifying the properties of the game window, such as the title and the size and the properties of the OpenGL context, like the [anti-aliasing](http://en.wikipedia.org/wiki/Anti-aliasing) level. Your application will then initiate the event loop, which contains an important set of tasks that need to be completed over and over again until the window closes. These tasks are are usually handling new window events like mouse clicks, updating the rendering state and then drawing.

This program flow would look something like this in pseudocode:

	#include <libraryheaders>

	int main()
	{
		createWindow( title, width, height );
		createOpenGLContext( settings );

		while ( windowOpen )
		{
			while ( event = newEvent() )
				handleEvent( event );

			updateScene();

			drawGraphics();
			presentGraphics();
		}

		return 0;
	}

When rendering a frame, the results will be stored in an offscreen buffer known as the *back buffer* to make sure the user only sees the final result. The `presentGraphics()` call will copy the result from the back buffer to the visible window buffer, the *front buffer*. Every application that makes use of realtime graphics will have a program flow that comes down to this, whether it uses a library or native code.

By default, libraries will create an OpenGL context that supports the legacy functions. This is unfortunate, because we're not interested in those and they may become unavailable at some point in the future. The good news is that it is possible to inform the drivers that our application is ready for the future and does not depend on the old functions. The bad news is that at this moment only the GLFW library allows us to specify this. This little shortcoming doesn't have any negative consequences right now, so don't let it influence your choice of library too much, but the advantage of a so-called core profile context is that accidentally calling any of the old functions results in an invalid operation error to set you straight.

Supporting resizable windows with OpenGL introduces some complexities as resources need to be reloaded and buffers need to be recreated to fit the new window size. It's more convenient for the learning process to not bother with such details yet, so we'll only deal with fixed size and fullscreen windows for now.

Libraries
========

There are many libraries around that can create a window and an accompanying OpenGL context for you. There is no best library out there, because everyone out there has different needs and ideals. I've chosen to discuss the process for the three most popular libraries here for completeness, but you can find more detailed guides on their respective websites. All code after this chapter will be independent of your choice of library here.

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
		sf::Sleep( 1.f );
		return 0;
	}

It should show a console application and exit after a second. If you run into any trouble, you can find more detailed information for [Visual Studio](http://www.sfml-dev.org/tutorials/1.6/start-vc.php), [Code::Blocks](http://www.sfml-dev.org/tutorials/1.6/start-cb.php) and [gcc](http://www.sfml-dev.org/tutorials/1.6/start-linux.php) in the tutorials on the SFML website.

Code
--------

Start by including the window package and defining the entry point of your application.

	#include <SFML/Window.hpp>

	int main()
	{
		return 0;
	}

A window can be opened by creating a new instance of `sf::Window`. The basic constructor takes an `sf::VideoMode` structure, a title for the window and a window style. The `sf::VideoMode` structure specifies the width, height and optionally the pixel depth of the window. Finally, the requirement for a fixed size window is specified by overriding the default style of `Style::Resize|Style::Close`. It is also possible to create a fullscreen window by passing `Style::Fullscreen` as window style.

	sf::Window window( sf::VideoMode( 800, 600 ), "OpenGL", sf::Style::Close );

The constructor can also take an `sf::WindowSettings` structure that allows you to specify the anti-aliasing level and the accuracy of the depth and stencil buffers. The latter two will be discussed later, so you don't have to worry about these yet.

When running this, you'll notice that the application instantly closes after creating the window. Let's add the event loop to deal with that.

	while ( window.IsOpened() )
	{
		sf::Event windowEvent;
		while ( window.GetEvent( windowEvent ) )
		{

		}
	}

When something happens to your window, an event is posted to the event queue. There are is a wide variety of events, including window size changes, mouse movement and key presses. It's up to you to decide which events require additional action, but there is at least one that needs to be handled to make your application run well.

	switch ( windowEvent.Type )
	{
	case sf::Event::Closed:
		window.Close();
		break;
	}

When the user attempts to close the window, the `Closed` event is fired and we act on that by closing the window. Try removing that line and you'll see that it's impossible to close the window by normal means. If you prefer a fullscreen window, you should add the escape key as a means to close the window:

	case sf::Event::KeyPressed:
		if ( windowEvent.Key.Code == sf::Key::Escape )
			window.Close();
		break;

You have your window and the important events are acted upon, so you're now ready to put something on the screen. After drawing something, you can swap the back buffer and the front buffer with `window.Display()`.

When you run your application, you should see something like this:

<img src="media/img/c1_window.png" alt="" />

Note that SFML allows you to have multiple windows. If you want to make use of this feature, make sure to call `window.SetActive()` to activate a certain window for drawing operations.

Now that you have a window and a context, there's [one more thing](#Onemorething) that needs to be done.

SDL
========

SDL comes with many different modules, but for creating a window with an accompanying OpenGL context we're only interested in the video module. It will take care of everything we need, so let's see how to use it.

Building
--------

After you've downloaded the SDL binaries or compiled them yourself, you'll find the needed files in the `lib` and `include` folders.

- Add the `lib` folder to your library path and link with `SDL` and `SDLmain`.
- SDL uses dynamic linking, so make sure that the shared library (`SDL.dll`, `SDL.so`) is with your executable.
- Add the `include` folder to your include path.

> SDL requires you to use the full prototype for `main`, you will get linker errors if you don't specify the prototype with command-line arguments.

To verify that you're ready, try compiling and running the following snippet of code:

	#include <SDL.h>

	int main( int argc, char *argv[] )
	{
		SDL_Init( SDL_INIT_EVERYTHING );
		
		SDL_Delay( 1000 );

		SDL_Quit();
		return 0;
	}

It should show a console application and exit after a second. If you run into any trouble, you can find more [detailed information](http://lazyfoo.net/SDL_tutorials/lesson01/index.php) for all kinds of platforms and compilers in the tutorials on the web.

Code
--------

Start by defining the entry point of your application and include the headers for SDL.

	#include <SDL.h>
	#include <SDL_opengl.h>

	int main( int argc, char *argv[] )
	{
		return 0;
	}

To use SDL in an application, you need to tell SDL which modules you need and when to unload them. You can do this with two lines of code.

	SDL_Init( SDL_INIT_VIDEO );
	...
	SDL_Quit();
	return 0;

The `SDL_Init` function takes a bitfield with the modules to load. The video module includes everything you need to create a window with an associated OpenGL context. The `SDL_Surface` structure quite simply represents an area that can be drawn to.

	SDL_Surface* surface =
	  SDL_SetVideoMode( 800, 600, 32, SDL_HWSURFACE | SDL_DOUBLEBUF | SDL_OPENGL );
	SDL_WM_SetCaption( "OpenGL", 0 );

The first three arguments are respectively for the width, height and pixel depth of the window. A pixel depth of 32 is what you want for nearly all modern systems. After that comes a few flags that specify what the surface should be capable of:

- *SDL_HWSURFACE* - Drawing should be hardware accelerated.
- *SDL_DOUBLEBUF* - Enable the back buffer mechanism that was discussed earlier.
- *SDL_OPENGL* - The surface should be capable of OpenGL.
- **Optional** *SDL_FULLSCREEN* - The surface should be fullscreen.

The reason the OpenGL flag is optional is because SDL also allows you to draw to the pixels on the surface directly. This is useful for software renderers and games that are specifically oriented around pixels, like falling sand games.

Then comes the most important part of the program, the event loop:

	SDL_Event windowEvent;
	while ( true )
	{
		if ( SDL_PollEvent( &windowEvent ) )
		{
			if ( windowEvent.type == SDL_QUIT ) break;
		}

		SDL_GL_SwapBuffers();
	}

The `SDL_PollEvent` function will check if there are any new events that have to be handled. An event can be anything from a mouse click to the user moving the window. Right now, the only event you need to respond to is the user pressing the little X button in the corner of the window. By breaking from the main loop, `SDL_Quit` is called and the window and graphics surface are destroyed. `SDL_GL_SwapBuffers` here takes care of swapping the front and back buffer after new things have been drawn by your application.

If you have a fullscreen window, it would be preferable to use escape as a means to close the window.

	if ( windowEvent.type == SDL_KEYUP &&
		windowEvent.key.keysym.sym == SDLK_ESCAPE ) break;

When you run your application now, you should see something like this:

<img src="media/img/c1_window.png" alt="" />

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

	#include <GL/glfw.h>

	int main()
	{
		glfwInit();
		glfwSleep( 1.0 );
		glfwTerminate();
	}

It should show a console application and exit after a second. If you run into any trouble, just ask in the comments and you'll receive help.

Code
--------

Start by simply including the GLFW header and define the entry point of the application.

	#include <GL/glfw.h>

	int main()
	{
		return 0;
	}

To use GLFW, it needs to be initialised when the program starts and you need to give it a chance to clean up when your program closes. The `glfwInit` and `glfwTerminate` functions are geared towards that purpose.

	glfwInit();
	...
	glfwTerminate();

The next thing to do is creating and configuring the window. GLFW allows only one window at a time, so there's no window object to keep track of.

	glfwOpenWindowHint( GLFW_OPENGL_VERSION_MAJOR, 3 );
	glfwOpenWindowHint( GLFW_OPENGL_VERSION_MINOR, 2 );
	glfwOpenWindowHint( GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE );

	glfwOpenWindowHint( GLFW_WINDOW_NO_RESIZE, GL_TRUE );
	glfwOpenWindow( 800, 600, 0, 0, 0, 0, 0, 0, GLFW_WINDOW );
	glfwSetWindowTitle( "OpenGL" );

You'll immediately notice the first three lines of code that are only relevant for this library. It is specified that we require the OpenGL context to support OpenGL 3.2 at the least. The `GLFW_OPENGL_PROFILE` option specifies that we want a context that only supports the new core functionality.

The purpose of `glfwSetWindowTitle` should be straight-forward, but `glfwOpenWindow` has a quite a lot of parameters. The first two specify the width and height of the drawing surface and the last parameter specifies the mode of the window. The mode must be either `GLFW_WINDOW` or `GLFW_FULLSCREEN`. The other parameters specify the pixel depth and the stencil and depth buffer accuracy. You don't need to worry about the latter two until you start using them. By passing `0` to the color depth parameters, the default pixel depth will be selected without an alpha channel. This is perfectly suitable for all regular graphics applications. The `glfwOpenWindowHint` function is used to specify additional requirements for the window.

Next comes the event loop, which in the case of GLFW works a little differently than the other libraries. GLFW uses a so-called *closed* event loop, which means you only have to handle events when you need to. That means your event loop will look really simple:

	while( glfwGetWindowParam( GLFW_OPENED ) )
	{
		glfwSwapBuffers();
	}

The only required function in the loop is `glfwSwapBuffers` to swap the back buffer and front buffer after you've finished drawing. If you are making a fullscreen application, you should also handle ESC to easily return to the desktop.

	if ( glfwGetKey( GLFW_KEY_ESC ) == GLFW_PRESS )
		break;

If you want to learn more about handling input, you can refer to chapter 4 of the [manual](http://www.glfw.org/GLFWUsersGuide274.pdf).

<img src="media/img/c1_window.png" alt="" />

You should now have a window or a full screen surface with an OpenGL context. Before you can start drawing stuff however, there's [one more thing](#Onemorething) that needs to be done.

One more thing
========

Unfortunately, we can't just call the functions we need yet. This is because it's the duty of the graphics card vendor to implement OpenGL functionality in their drivers based on what the graphics card supports. You wouldn't want your program to only be compatible with a single driver version and graphics card, so we'll have to do something clever.

Your program needs to check which functions are available at runtime and link with them dynamically. This is done by finding the addresses of the functions, assigning them to function pointers and calling them. That looks something like this:

	// Specify prototype of function
	typedef void (*GENBUFFERS) ( GLsizei, GLuint* );

	// Load address of function and assign it to a function pointer
	GENBUFFERS glGenBuffers = (GENBUFFERS)wglGetProcAddress( "glGenBuffers" );
	// or Linux:
	GENBUFFERS glGenBuffers = (GENBUFFERS)glXGetProcAddress( "glGenBuffers" );
	// or OSX:
	GENBUFFERS glGenBuffers = (GENBUFFERS)NSGLGetProcAddress( "glGenBuffers" );

	// Call function as normal
	int buffer;
	glGenBuffers( 1, &buffer );

Let me begin by asserting that it is perfectly normal to be scared by this snippet of code. You may not be familiar with the concept of function pointers yet, but at least try to roughly understand what is happening here. You can imagine that going through this process of defining prototypes and finding addresses of functions is very tedious and in the end nothing more than a complete waste of time.

The good news is that there are libraries that have solved this problem for us. The most popular and best maintained library right now is *GLEW* and there's no reason for that to change anytime soon. Nevertheless, the alternative library *GLEE* works almost completely the same save for the initialization and cleanup code.

If you haven't built GLEW yet, do so now. We'll now add GLEW to your project.

* Start by linking your project with the static GLEW library in the `lib` folder. This is either `glew32s.lib` or `GLEW` depending on your platform.
* Add the `include` folder to your include path.

Now just include the header in your program, but make sure that it is included before the OpenGL headers or the library you used to create your window.

	#define GLEW_STATIC
	#include <GL/glew.h>

Don't forget to define `GLEW_STATIC` either using this preprocessor directive or by adding the `-DGLEW_STATIC` directive to your compiler commandline parameters or project settings.

> If you prefer to dynamically link with GLEW, leave out the define and link with `glew32.lib` instead of `glew32s.lib` on Windows. Don't forget to include `glew32.dll` or `libGLEW.so` with your executable!

Now all that's left is calling `glewInit()` after the creation of your window and OpenGL context. The `glewExperimental` line is necessary to force GLEW to use a modern OpenGL method for checking if a function is available.

	glewExperimental = GL_TRUE;
	glewInit();

Make sure that you've set up your project correctly by calling the `glGenBuffers` function, which was loaded by GLEW for you!

	GLuint vertexBuffer;
	glGenBuffers( 1, &vertexBuffer );

	printf( "%u\n", vertexBuffer );

Your program should compile and run without issues and display the number `1` in your console. If you need more help with using GLEW, you can refer to the [website](http://glew.sourceforge.net/install.html) or ask in the comments.

Now that we're past all of the configuration and initialization work, I'd advise you to make a copy of your current project so that you won't have to write all of the boilerplate code again when starting a new project.

Now, let's get to [drawing things](/drawing)!
