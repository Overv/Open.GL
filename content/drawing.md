The graphics pipeline
========

By learning OpenGL, you've decided that you want to do all of the hard work yourself. That inevitably means that you'll be thrown in the deep, but once you understand the essentials, you'll see that doing things *the hard way* doesn't have to be so difficult after all. To top that all, the exercises at the end of this chapter will show you the sheer amount of control you have over the rendering process by doing things the modern way!

The *graphics pipeline* covers all of the steps that follow each other up on processing the input data to get to the final output image. I'll explain these steps with help of the following illustration.

<img src="media/img/c2_pipeline.png" alt="" />

It all begins with the *vertices*, which are the points that shapes like triangles will later be contructed from. Each of these points is stored with certain attributes and it's up to you to decide what kind of attributes you want to store. Commonly used attributes are 3D position in the world and texture coordinates.

The *vertex shader* is a small program running on your graphics card that processes every one of these input vertices individually. This is where the perspective transformation takes place, which projects vertices with a 3D world position onto your 2D screen! It also passes important attributes like color and texture coordinates further down the pipeline.

After the input vertices have been transformed, the graphics card will form triangles, lines or points out of them. These shapes are called *primitives* because they form the basis of more complex shapes. There are some additional drawing modes to choose from, like triangle strips and line strips. These reduce the amount of vertices you need to pass if you want to create objects where each next primitive is connected to the last one, like a continuous line consisting of several segments.

The following step, the *geometry shader*, is completely optional and was only recently introduced. Unlike the vertex shader, the geometry shader can output more data than comes in. It takes the primitives from the shape assembly stage as input and can either pass a primitive through down to the rest of the pipeline, modify it first, completely discard it or even replace it with other primitive(s). Since the communication between the GPU and the rest of the PC is relatively slow, this stage can help you reduce the amount of data that needs to be transfered. With a voxel game for example, you could pass vertices as point vertices, along with an attribute for their world position, color and material and the actual cubes can be produced in the geometry shader with a point as input!

After the final list of shapes is composed and converted to screen coordinates, the rasterizer turns the visible parts of the shapes into pixel-sized *fragments*. The vertex attributes coming from the vertex shader or geometry shader are interpolated and passed as input to the fragment shader for each fragment. As you can see in the image, the colors are smoothly interpolated over the fragments that make up the triangle, even though only 3 points were specified.

The *fragment shader* is processes each individual fragment along with its interpolated attributes and should output the final color. This is usually done by sampling from a texture using the interpolated texture coordinate vertex attributes or simply outputting a color. In more advanced scenarios, there could also be calculations related to lighting and shadowing in this program. The shader also has the ability to discard certain fragments, which means that a shape will be see-through there.

Finally, the end result is composed from all these shape fragments by blending them together and performing depth and stencil testing. All you need to know about these last two right now, is that they allow you to use additional rules to throw away certain fragments and let others pass. For example, if one triangle is obscured by another triangle, the fragment of the closer triangle should end up on the screen.

Now that you know how your graphics card turns an array of vertices into an image on the screen, let's get to work!

One more thing
========

Unfortunately, we can't just link with some library, call the functions we need and be done with it. This is because it's the duty of the graphics card vendor to implement OpenGL functionality in their drivers based on what the graphics card supports. You wouldn't want your program to only be compatible with a single driver version and graphics card.

It's the job of your program to check which functions are available at runtime and link with them dynamically. This is done by finding the addresses of the functions, assigning them to function pointers and calling them. That looks something like this:

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

If you haven't built GLEW yet, do so now.

* Start by linking your project with the static GLEW library in the `lib` folder. This file will be called either `libGLEW.a` or `glew32s.lib` depending on your platform.
* Add the `include` folder to your include path.

Now just include the header in your program, but make sure that it is included before the OpenGL headers or the library you used to create your window.

	#define GLEW_STATIC
	#include <GL/glew.h>

Don't forget to define `GLEW_STATIC` either using this preprocessor directive or by adding the `-DGLEW_STATIC` directive to your compiler commandline parameters or project settings.

> If you prefer to dynamically link with GLEW, leave out the define and link with `glew32.lib` instead of `glew32s.lib` on Windows. Don't forget to include `glew32.dll` or `libGLEW.so` with your executable!

Now all that's left is calling `glewInit()` after the creation of your window and OpenGL context. Make sure that you've set up your project correctly by calling the `glGenBuffers` function, which was loaded by GLEW for you!

	glewInit();

	unsigned int vertexBuffer;
	glGenBuffers( 1, &vertexBuffer );

	printf( "%u\n", vertexBuffer );

Your program should compile and run without issues and display the number `1` in your console. If you need more help with using GLEW, you can refer to the [website](http://glew.sourceforge.net/install.html) or ask in the comments.