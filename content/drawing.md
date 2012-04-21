The graphics pipeline
========

By learning OpenGL, you've decided that you want to do all of the hard work yourself. That inevitably means that you'll be thrown in the deep, but once you understand the essentials, you'll see that doing things *the hard way* doesn't have to be so difficult after all. To top that all, the exercises at the end of this chapter will show you the sheer amount of control you have over the rendering process by doing things the modern way!

The *graphics pipeline* covers all of the steps that follow each other up on processing the input data to get to the final output image. I'll explain these steps with help of the following illustration.

<img src="media/img/c2_pipeline.png" alt="" />

It all begins with the *vertices*, which are the points that shapes like triangles will later be contructed from. Each of these points is stored with certain attributes and it's up to you to decide what kind of attributes you want to store. Commonly used attributes are 3D position in the world and texture coordinates.

The *vertex shader* is a small program running on your graphics card that operates on every one of these input vertices individually. This is the place where the perspective transformation takes place, which projects vertices with a 3D world position onto your 2D screen! It also passes important attributes like color and texture coordinates further down the pipeline.

After the shapes have been assembled from the output of the vertex shader, the rasterizer turns the visible parts of the shapes into pixel-sized *fragments*. The vertex attributes coming from the vertex shader are interpolated and passed as input to the fragment shader for each fragment. As you can see in the image, the colors are smoothly spread over the fragments that make up the triangle, even though only 3 points were specified.

The *fragment shader* is quite similar to the vertex shader, only different in the fact that it operates on individual fragments as opposed to vertices. It takes the interpolated vertex attributes as input and the program should compute a final color for the fragment as output. This is usually done by sampling from a texture using the interpolated texture coordinate vertex attributes.

Finally, the end result is composed from all these shape fragments by blending them together and performing depth and stencil testing. All you need to know about these last two right now, is that they allow you to use additional rules to throw away certain fragments and let others pass. For example, if one triangle is obscured by another triangle, the fragment of the closer triangle should end up on the screen.

Now that you know how your graphics card turns an array of vertices into an image on the screen, let's get to work!