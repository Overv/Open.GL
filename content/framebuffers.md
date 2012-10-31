Framebuffers
========

In the previous chapters we've looked at the different types of buffers OpenGL offers: the color, depth and stencil buffers. These buffers occupy video memory like any other OpenGL object, but so far we've had little control over them besides specifying the pixel formats when you created the OpenGL context. This combination of buffers is known as the default *framebuffer* and as you've seen, a framebuffer is an area in memory that can be rendered to. What if you want to take a rendered result and do some additional operations on it, such as post-processing as seen in many modern games?

In this chapter we'll look at *framebuffer objects*, which are a means of creating additional framebuffers to render to. The great thing about framebuffers is that they allow you to render a scene directly to a texture, which can then be used in other rendering operations. After discussing how framebuffer objects work, I'll show you how to use them to do post-processing on the scene from the previous chapter.

Creating a new framebuffer
--------

The first thing you need is a framebuffer object to manage your new framebuffer.

	GLuint frameBuffer;
	glGenFramebuffers( 1, &frameBuffer );

You can not use this framebuffer yet at this point, because it is not *complete*. A framebuffer is generally complete if:

- At least one buffer has been attached (e.g. color, depth, stencil)
- There must be at least one color attachment *(OpenGL 4.1 and earlier)*
- All attachments are complete
- All attachments must have the same number of multisamples

You can check if a framebuffer is complete at any time by calling `glCheckFramebufferStatus` and check if it returns `GL_FRAMEBUFFER_COMPLETE`. See the [reference](http://www.opengl.org/sdk/docs/man3/xhtml/glCheckFramebufferStatus.xml) for other return values. You don't have to do this check, but it's usually a good thing to verify, just like checking if your shaders compiled successfully.

Now, let's bind the framebuffer to work with it.

	glBindFramebuffer( GL_FRAMEBUFFER, frameBuffer );

The first parameter specifies the framebuffer the image should be attached to. OpenGL makes a distinction here between `GL_DRAW_FRAMEBUFFER` and `GL_READ_FRAMEBUFFER`. The framebuffer bound to read is used in calls to `glReadPixels`, but since this distinction in normal applications is fairly rare, you can have your actions apply to both by using `GL_FRAMEBUFFER`.

	glDeleteFramebuffers( 1, &frameBuffer );

Don't forget to clean up after you're done.

Attachments
--------

Your framebuffer can only be used as a render target if memory has been allocated to store the results. This is done by attaching *images* for each buffer (color, depth, stencil or a combination of depth and stencil). There are two kinds of objects that can function as images: texture objects and *renderbuffer objects*. The advantage of the former is that they can be directly used in shaders as seen in the previous chapters, but renderbuffer objects may be more optimized specifically as render targets depending on your implementation.

Texture images
--------

We'd like to be able to render a scene and then use the result in the color buffer in another rendering operation, so a texture is ideal in this case. Creating a texture for use as an image for the color buffer of the new framebuffer is as simple.

	GLuint texColorBuffer;
	glGenTextures( 1, &texColorBuffer );
	glBindTexture( GL_TEXTURE_2D, texColorBuffer );

	glTexImage2D(
		GL_TEXTURE_2D, 0, GL_RGB, 800, 600, 0, GL_RGB, GL_UNSIGNED_BYTE, NULL
	);

	glTexParameteri( GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR );
	glTexParameteri( GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR );

The difference between this texture and the textures you've seen before is the `NULL` value for the data parameter. That makes sense, because the data is going to be created dynamically this time with rendering operations. Since this is the image for the color buffer, the `format` and `internalformat` parameters are a bit more restricted. The `format` parameter will typically be limited to either `GL_RGB` or `GL_RGBA` and the `internalformat` to the color formats.

I've chosen the default RGB internal format here, but you can experiment with more exotic formats like `GL_RGB10` if you want 10 bits of color precision. My application has a resolution of 800 by 600 pixels, so I've made this new color buffer match that. The resolution doesn't have to match the one of the default framebuffer, but don't forget a `glViewport` call if you do decide to vary.

The one thing that remains is attaching the image to the framebuffer.

	glFramebufferTexture2D(
		GL_FRAMEBUFFER, GL_COLOR_ATTACHMENT0, GL_TEXTURE_2D, texColorBuffer, 0
	);

The second parameter implies that you can have multiple color attachments. A fragment shader can output different data to any of these by linking `out` variables to attachments with the `glBindFragDataLocation` function we used earlier. We'll stick to one output for now. The last parameter specifies the mipmap level the image should be attached to. Mipmapping is not of any use, since the color buffer image will be rendered at its original size when using it for post-processing.

Renderbuffer Object images
--------

As we're using a depth and stencil buffer to render the spinning cube of cuteness, we'll have to create them as well. OpenGL allows you to combine those into one image, so we'll have to create just one more before we can use the framebuffer. Although we could do this by creating another texture, it is more efficient to store these buffers in a Renderbuffer Object, because we're only interested in reading the color buffer in a shader.

	GLuint rboDepthStencil;
	glGenRenderbuffers( 1, &rboDepthStencil );
	glBindRenderbuffer( GL_RENDERBUFFER, rboDepthStencil );
	glRenderbufferStorage( GL_RENDERBUFFER, GL_DEPTH24_STENCIL8, 800, 600 );

Creating a renderbuffer object is very similar to creating a texture, the difference being is that this object is designed to be used as image instead of a general purpose data buffer like a texture. I've chosen the `GL_DEPTH24_STENCIL8` internal format here, which is suited for holding both the depth and stencil buffer with 24 and 8 bits of precision respectively.

	glFramebufferRenderbuffer(
	  GL_FRAMEBUFFER, GL_DEPTH_STENCIL_ATTACHMENT, GL_RENDERBUFFER, rboDepthStencil
	);

Attaching it is easy as well. You can delete this object like any other object at a later time with a call to `glDeleteRenderbuffers`.

Using a framebuffer
--------

Selecting a framebuffer as render target is very easy, in fact it can be done with a single call.

	glBindFramebuffer( GL_FRAMEBUFFER, frameBuffer );

After this call, all rendering operations will store their result in the attachments of the newly created framebuffer. To switch back to the default framebuffer visible on your screen, simply pass `0`.

	glBindFramebuffer( GL_FRAMEBUFFER, 0 );

Note that although only the default framebuffer will be visible on your screen, you can read any framebuffer that is currently bound with a call to `glReadPixels` as long as it's not only bound to `GL_DRAW_FRAMEBUFFER`.