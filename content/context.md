> The current format of this chapter is not appropriate. Each library has approximately the same code flow with a few different functions and values here and there, but the principles are the same, so they should not be discussed separately. It will only introduce confusion and a lot of unnecessary repetition. The introduction needs to be altered to include these concepts and only then should there be a short explanation for how that applies to each of the listed libraries. It also needs to be more streamlined. The seemingly random *framebuffer* notice may be inappropriate. **The introduction should be written in such a way that the library subsections only contain build info and a full code snippet with some comments. It should be clear that info is included for completeness and that more detailed guides are available on the official websites.**

Window and OpenGL context
========

Before you can start drawing things, you need to initialize OpenGL. This is done by creating an OpenGL context, which is essentially a state machine that stores all data related to the rendering of your application. When your application closes, the OpenGL context is destroyed and everything is cleaned up.

Among the things that are stored in this context is the so-called default *framebuffer*. A framebuffer is the final rendering destination in OpenGL and the default one will usually be a window.

The problem is that creating a window and an OpenGL context is not part of the OpenGL specification. That means it is done differently on every platform out there! Developing applications using OpenGL is all about being portable, so this is the last thing we need. Luckily there are libraries out there that abstract this process, so that you can maintain the same codebase for all supported platforms.

Like with all things, there are multiple options available that are discussed below.

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

- Add the `lib` folder to your library path and link with `sfml-system-s` and `sfml-window-s`. If you're using Visual Studio, use `lib/vs2008` instead.
- Add the `include` folder to your include path.

> The SFML libraries have a simply naming convention for different configurations. If you want to dynamically link, simply remove the `-s` from the name, define `SFML_DYNAMIC` and copy the shared libraries. If you want to use the binaries with debug symbols, additionally append `-d` to the name.

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

A window can be opened by creating a new instance of `sf::Window`. The constructor takes an optional `WindowSettings` structure, but I will not discuss these settings here because they require knowledge of OpenGL concepts. The default values are perfectly adequate for this guide. The basic constructor takes a `sf::VideoMode` structure and a title for the window. The `sf::VideoMode` structure specifies the width, height and optionally the pixel depth of the window. Nearly all modern systems will support the default depth of 32 bits per pixel.

	sf::Window window( sf::VideoMode( 800, 600, 32 ), "OpenGL" );

When running this, you'll notice that the application instantly closes after creating the window. To do things with it, you need to listen for events and act on them. This will be the main loop in your application and it looks like this:

	while ( window.IsOpened() )
	{
		sf::Event windowEvent;
		while ( window.GetEvent( windowEvent ) )
		{

		}
	}

When something happens to your window, an event is posted to the event queue. Your application should keep picking up these events and act on them until the window closes and your application exits. There are is a wide variety of events, including window size changes, mouse movement and key presses. It's up to you to decide which events require additional action, but there are a few that need to be handled to make your application run well.

	switch ( windowEvent.Type )
	{
	case sf::Event::Closed:
		window.Close();
		break;

	case sf::Event::Resized:
		glViewport( 0, 0, windowEvent.Size.Width, windowEvent.Size.Height );
		break;
	}

When the user attempts to close the window, the `Closed` event is fired and we act on that by closing the window. Try removing that line and you'll see that it's impossible to close the window by normal means. The second event is a little bit more intriguing. It is obvious enough what the `Resized` event means, but what is that function? `glViewport` is an important OpenGL function that sets the viewport rectangle. The viewport is the area the rendered image will be mapped to, so it's important that this area matches the window size.

You have your window and the important events are acted upon, so you're now ready to put something on the screen. This is usually done in the main loop after handling the events. By using some additional basic OpenGL functions, let's try clearing the screen to red.

	glClearColor( 1.0f, 0.0f, 0.0f, 1.0f );
	glClear( GL_COLOR_BUFFER_BIT );

	window.Display();

`glClearColor` specifies the color the screen is cleared with. Notice that instead of the range of `0` to `255`, the color components need to be specified as floating point numbers between `0.0f` and `1.0f`. The screen is actually cleared by calling `glClear`. This function takes a parameter that specifies the buffers that should be cleared.

Buffers will be discussed in the next chapter, but in this case we only care about the color buffer. The call to `Display()` that is necessary to see the results is also directly related to these buffers.