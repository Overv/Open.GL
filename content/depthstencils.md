Extra buffers
========

Up until now there is only one type of output buffer you've made use of, the color buffer. This chapter will discuss two additional types, the *depth buffer* and the *stencil buffer*. For each of these a problem will be presented and subsequently solved with that specific buffer.

The depth and stencil buffers are optional, so you need to make sure that they're created before you try using them. SFML and SDL users can continue to the next section, but GLFW users will have to make a slight modification to their program.

	glfwOpenWindow( 800, 600, 0, 0, 0, 0, 24, 8, GLFW_WINDOW );

The `glfwOpenWindow` call above creates a 24 bit depth buffer and an 8 bit stencil buffer. These numbers determine the accuracy of the buffers.

Preparations
========

To best demonstrate the use of these buffers, let's draw a cube instead of a flat shape. The vertex shader needs to be modified to accept a third coordinate:

	in vec3 position;
	...
	gl_Position = proj * view * model * vec4( position, 1.0 );

Vertices are now 8 floats in size, so you'll have to update the vertex attribute offsets and strides as well. Finally, add the extra coordinate to the vertex array:

	float vertices[] = {
		-0.5f,  0.5f, 0.0f, 1.0f, 0.0f, 0.0f, 0.0f, 0.0f,
		 0.5f,  0.5f, 0.0f, 0.0f, 1.0f, 0.0f, 1.0f, 0.0f,
		 0.5f, -0.5f, 0.0f, 0.0f, 0.0f, 1.0f, 1.0f, 1.0f,
		-0.5f, -0.5f, 0.0f, 1.0f, 1.0f, 1.0f, 0.0f, 1.0f
	};

Confirm that you've made all the required changes by running your program and checking if it still draws a flat spinning image of a kitten blended with a puppy. A single cube consists of 36 vertices (6 sides * 2 triangles * 3 vertices), so I will ease your life by providing the array [here](content/code/c5_vertices.txt).

	glDrawArrays( GL_TRIANGLES, 0, 36 );

We will not make use of element buffers for drawing this cube, so you can use `glDrawArrays` to draw it. If you were confused by this explanation, you can compare your program to [this reference code](content/code/c5_cube.txt).

<div class="livedemo" id="demo_c5_cube" style="background: url( 'media/img/c5_window.png' )">
	<canvas width="640" height="480"></canvas>
	<script type="text/javascript" src="content/demos/c5_cube.js"></script>
</div>

It immediately becomes clear that the cube is not rendered as expected when seeing the output. The sides of the cube are being drawn, but they overlap each other in strange ways! The problem here is that when OpenGL draws your cube triangle-by-triangle, it will simply write over pixels even though something else may have been drawn there before. In this case OpenGL will happily draw triangles in the back over triangles at the front.

Luckily OpenGL offers ways of telling it when to draw over a pixel and when not to. I'll go over the two most important ways of doing that, depth testing and stencilling, in this chapter.

Depth buffer
========

*Z-buffering* is a way of keeping track of the depth of every pixel on the screen. The depth is proportional to the distance between the screen plane and a fragment that has been drawn. That means that the fragments on the sides of the cube further away from the viewer have a higher depth value, whereas fragments closer have a lower depth value.

If this depth is stored along with the color when a fragment is written, fragments drawn later can compare their depth to the existing depth to determine if the new fragment is closer to the viewer than the old fragment. If that is the case, it should be drawn over and otherwise it can simply be discarded. This is known as *depth testing*.

OpenGL offers a way to store these depth values in an extra buffer, called the *depth buffer*, and perform the required check for fragments automatically. The fragment shader will not run for fragments that are invisible, which can have a significant impact on performance. This functionality can be enabled by calling `glEnable`.

	glEnable( GL_DEPTH_TEST );

If you enable this functionality now and run your application, you'll notice that you get a black screen. That happens because the depth buffer is filled with 0 depth for each pixel by default. Since no fragments will ever be closer than that they are all discarded.

The depth buffer can be cleared along with the color buffer by extending the `glClear` call:

	glClear( GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT );

The default clear value for the depth is `1.0f`, which is equal to the depth of your far clipping plane and thus the furthest depth that can be represented. All fragments will be closer than that, so they will no longer be discarded. 

<div class="livedemo" id="demo_c5_depth" style="background: url( 'media/img/c5_window2.png' )">
	<canvas width="640" height="480"></canvas>
	<script type="text/javascript" src="content/demos/c5_depth.js"></script>
</div>

With the depth test capability enabled, the cube is now rendered correctly. Just like the color buffer, the depth buffer has a certain amount of bits of precision which can be specified by you. Less bits of precision reduce the extra memory use, but can introduce rendering errors in more complex scenes.

Stencil buffer
========

Will arrive shortly (in a few hours).