Introduction
========

This guide will teach you the basics of using OpenGL to develop modern graphics applications. There are a lot of other guides on this topic, but there are some major points where this guide differs from those. We will not be discussing any of the old parts of the OpenGL specification. That means you'll be taught how to implement things yourself, instead of using deprecated functions like `glBegin` and `glLight`. Anything that is not directly related to OpenGL itself, like creating a window and loading textures from files, will be done using a few small libraries.

To show you how much it pays off to do things yourself, we will be developing a voxel world renderer much like Minecraft throughout this guide. Each time you learn a new feature, you'll see how it can be used in practice by adding something to our game. This guide also contains a lot of interactive examples to make it both fun and easy to learn all the different aspects of using a low-level graphics library like OpenGL!

Prerequisites
========

Before we can take off, you need to make sure you have all the things you need.

* A reasonable amount of experience with C++
* Graphics card [compatible](http://en.wikipedia.org/wiki/OpenGL#OpenGL_3.2) with OpenGL 3.2
* [SFML](http://www.sfml-dev.org/), [GLFW](http://www.glfw.org/) or [SDL](http://www.libsdl.org/) for creating the context and handling input
* [GLEW](http://glew.sourceforge.net/) to use newer OpenGL functions
* [GLM](http://glm.g-truc.net/) for vectors and matrices

Context creation will be explained for *SFML*, *GLFW* and *SDL*, so use whatever library suites you best. See the next chapter for the differences between the three if you're not sure which one to use.

> You also have the option of creating the context yourself using Win32 or Xlib, but your code will not be portable anymore. That means you can not use the same code for all platforms.

If you've got everything you need, let's [begin](/context).