Introduction
============

This guide will teach you the basics of using OpenGL to develop modern graphics
applications. There are a lot of other guides on this topic, but there are some
major points where this guide differs from those. We will not be discussing any
of the old parts of the OpenGL specification. That means you'll be taught how to
implement things yourself, instead of using deprecated functions like `glBegin`
and `glLight`. Anything that is not directly related to OpenGL itself, like
creating a window and loading textures from files, will be done using a few
small libraries.

To show you how much it pays off to do things yourself, this guide also contains
a lot of interactive examples to make it both fun and easy to learn all the
different aspects of using a low-level graphics library like OpenGL!

As an added bonus, you always have the opportunity to ask questions at the end
of each chapter in the comments section. I'll try to answer as many questions as
possible, but always remember that there are plenty of people out there who are
willing to help you with your issues. Make sure to help us help you by
specifying your platform, compiler, the relevant code section, the result you
expect and what is actually happening.

Credits
=======

Thanks to all of the [contributors](https://github.com/Overv/Open.GL/graphs/contributors)
for their help with improving the quality of this tutorial! Special thanks to
the following people for their essential contributions to the site:

* [Toby Rufinus](https://github.com/NightPixel) (code fixes, improved images, sample solutions for last chapters)
* [Eric Engeström](https://github.com/1ace) (making the site mobile friendly)
* [Elliott Sales de Andrade](https://github.com/QuLogic) (improving article text)
* [Aaron Hamilton](https://github.com/xorgy) (improving article text)

Prerequisites
=============

Before we can take off, you need to make sure you have all the things you need.

* A reasonable amount of experience with C++
* Graphics card [compatible](http://en.wikipedia.org/wiki/OpenGL#OpenGL_3.2) with OpenGL 3.2
* [SFML](http://www.sfml-dev.org/), [GLFW](http://www.glfw.org/) or [SDL](http://www.libsdl.org/) for creating the context and handling input
* [GLEW](http://glew.sourceforge.net/) to use newer OpenGL functions
* [SOIL](http://www.lonesock.net/soil.html) for textures
* [GLM](http://glm.g-truc.net/) for vectors and matrices

Context creation will be explained for *SFML*, *GLFW* and *SDL*, so use whatever library suites you best. See the next chapter for the differences between the three if you're not sure which one to use.

> You also have the option of creating the context yourself using Win32, Xlib or Cocoa, but your code will not be portable anymore. That means you can not use the same code for all platforms.

If you've got everything you need, let's [begin](/context).
