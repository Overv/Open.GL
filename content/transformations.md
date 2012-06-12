Matrices
========

Since this is a guide on graphics programming, this chapter will not cover a lot of the extensive theory behind matrices. Only the theory that applies to their use in computer graphics will be considered here and they will be explained from a programmer's perspective. If you want to learn more about the topic, [these Khan Academy videos](http://www.khanacademy.org/math/algebra/algebra-matrices/v/introduction-to-matrices) are a really good general introduction to the subject.

A matrix is a rectangular array of mathematical expressions, much like a two-dimensional array. Below is an example of a matrix displayed in the common square brackets form.

\[a = \begin{bmatrix} 1 & 2 \\ 3 & 4 \\ 5 & 6 \end{bmatrix}\]

Matrices use a coordinate system `(i,j)` where `i` is the row and `j` is the column. That is why the matrix displayed above is called a 3-by-2 matrix. To refer to a specific value in the matrix, for example `5`, the \(a_{31}\) notation is used.

Basic operations
========

To get a bit more familiar with the concept of an array of numbers, let's first look at a few basic operations.

Addition and subtraction
--------

Just like regular numbers, the addition and subtraction operators are also defined for matrices. The only requirement is that the two operands have exactly the same row and column dimensions.

\[\begin{bmatrix} 3 & 2 \\ 0 & 4 \end{bmatrix} + \begin{bmatrix} 4 & 2 \\ 2 & 2 \end{bmatrix} = \begin{bmatrix} 3+4 & 2+2 \\ 0+2 & 4+2 \end{bmatrix} = \begin{bmatrix} 7 & 4 \\ 2 & 6 \end{bmatrix}\]

\[\begin{bmatrix} 4 & 2 \\ 2 & 7 \end{bmatrix} - \begin{bmatrix} 3 & 2 \\ 0 & 4 \end{bmatrix} = \begin{bmatrix} 4-3 & 2-2 \\ 2-0 & 2-4 \end{bmatrix} = \begin{bmatrix} 1 & 0 \\ 2 & 3 \end{bmatrix}\]

The values in the matrices are individually added or subtracted from each other.

Scalar product
--------

The product of a scalar and a matrix is as straightforward as addition and subtraction.

\[2 \cdot \begin{bmatrix} 1 & 2 \\ 3 & 4 \end{bmatrix} = \begin{bmatrix} 2 & 4 \\ 6 & 8 \end{bmatrix}\]

The values in the matrices are each multiplied by the scalar.

Matrix-Vector product
========

The product of a matrix with another matrix is quite a bit more involved and is often misunderstood, so for simplicity's sake I will only mention the specific cases that apply to graphics programming. To see how matrices are actually used to transform vectors, we'll first dive into the product of a matrix and a vector.

\[\begin{bmatrix} \color{red}a & \color{red}b & \color{red}c & \color{red}d \\ \color{blue}e & \color{blue}f & \color{blue}g & \color{blue}h \\ \color{green}i & \color{green}j & \color{green}k & \color{green}l \\ \color{purple}m & \color{purple}n & \color{purple}o & \color{purple}p \end{bmatrix} \begin{pmatrix} x \\ y \\ z \\ 1 \end{pmatrix} = \begin{pmatrix} \color{red}a\cdot x + \color{red}b\cdot y + \color{red}c\cdot z + \color{red}d\cdot 1 \\ \color{blue}e\cdot x + \color{blue}f\cdot y + \color{blue}g\cdot z + \color{blue}h\cdot 1 \\ \color{green}i\cdot x + \color{green}j\cdot y + \color{green}k\cdot z + \color{green}l\cdot 1 \\ \color{purple}m\cdot x + \color{purple}n\cdot y + \color{purple}o\cdot z + \color{purple}p\cdot 1 \end{pmatrix}\]

To calculate the product of a matrix and a vector, the vector is written as a 4-by-1 matrix. The expressions to the right of the equals sign show how the new `x`, `y` and `z` values are calculated after the vector has been transformed. For those among you who aren't very math savvy, the dot is a multiplication sign.

I will mention each of the common vector transformations in this section and how a matrix can be formed that performs them. For completeness, let's first consider a transformation that does absolutely nothing.

\[\begin{bmatrix} \color{red}1 & \color{red}0 & \color{red}0 & \color{red}0 \\ \color{blue}0 & \color{blue}1 & \color{blue}0 & \color{blue}0 \\ \color{green}0 & \color{green}0 & \color{green}1 & \color{green}0 \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix} \begin{pmatrix} x \\ y \\ z \\ 1 \end{pmatrix} = \begin{pmatrix} \color{red}1\cdot x + \color{red}0\cdot y + \color{red}0\cdot z + \color{red}0\cdot 1 \\ \color{blue}0\cdot x + \color{blue}1\cdot y + \color{blue}0\cdot z + \color{blue}0\cdot 1 \\ \color{green}0\cdot x + \color{green}0\cdot y + \color{green}1\cdot z + \color{green}0\cdot 1 \\ \color{purple}0\cdot x + \color{purple}0\cdot y + \color{purple}0\cdot z + \color{purple}1\cdot 1 \end{pmatrix} = \begin{pmatrix} \color{red}1\cdot x \\ \color{blue}1\cdot y \\ \color{green}1\cdot z \\ \color{purple}1\cdot 1 \end{pmatrix}\]

This matrix is called the *identity matrix*, because just like the number `1`, it will always return the value it was originally multiplied by.

Let's look at the most common vector transformations now and deduce how a matrices can be formed from them.

Translation
--------

To see why we're working with 4-by-1 vectors and subsequently 4-by-4 transformation matrices, let's see how a translation matrix is formed. A translation moves a vector a certain distance in a certain direction.

<img src="media/img/c4_translation.png" alt="" />

Can you guess from the multiplication overview what the matrix should look like to translate a vector by `(X,Y,Z)`?

\[\begin{bmatrix} \color{red}1 & \color{red}0 & \color{red}0 & \color{red}X \\ \color{blue}0 & \color{blue}1 & \color{blue}0 & \color{blue}Y \\ \color{green}0 & \color{green}0 & \color{green}1 & \color{green}Z \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix} \begin{pmatrix} x \\ y \\ z \\ 1 \end{pmatrix} = \begin{pmatrix} x+\color{red}X\cdot 1 \\ y+\color{blue}Y\cdot 1 \\ z+\color{green}Z\cdot 1 \\ 1 \end{pmatrix}\]

Without the fourth column and the bottom `1` value a translation wouldn't have been possible.

Scaling
--------

A scale transformation scales each of a vector's components by a (different) scalar. It is commonly used to shrink or stretch a vector as demonstrated below.

<img src="media/img/c4_scaling.png" alt="" />

If you understand how the previous matrix was formed, it should not be difficult to come up with a matrix that scales a given vector by `(SX,SY,SZ)`.

\[\begin{bmatrix} \color{red}{SX} & \color{red}0 & \color{red}0 & \color{red}0 \\ \color{blue}0 & \color{blue}{SY} & \color{blue}0 & \color{blue}0 \\ \color{green}0 & \color{green}0 & \color{green}{SZ} & \color{green}0 \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix} \begin{pmatrix} x \\ y \\ z \\ 1 \end{pmatrix} = \begin{pmatrix} \color{red}{SX}\cdot x \\ \color{green}{SY}\cdot y \\ \color{blue}{SZ}\cdot z \\ 1 \end{pmatrix}\]

If you think about it for a moment, you can see that scaling would also be possible with a mere 3-by-3 matrix.

Rotation
--------

A rotation transformation rotates a vector around the origin `(0,0,0)` using a given *axis* and *angle*. To understand how the axis and the angle control a rotation, let's do a small experiment.

<img src="media/img/c4_rotation.png" alt="" />

Put your thumb up against your monitor and try rotating your hand around it. The object, your hand, is rotating around your thumb: the rotation axis. The further you rotate your hand away from its initial position, the higher the rotation angle.

In this way the rotation axis can be imagined as an arrow an object is rotating around. If you imagine your monitor to be a 2-dimensional XY surface, the rotation axis (your thumb) is pointing in the Z direction.

Objects can be rotated around any given axis, but for now only the X, Y and Z axis are important. You'll see later in this chapter that any rotation axis can be established by rotating around the X, Y and Z axis simultaneously.

The matrices for rotating around the three axes are specified here. The rotation angle is indicated by the theta (\(\theta\)).

Rotation around X-axis:
\[\begin{bmatrix} \color{red}1 & \color{red}0 & \color{red}0 & \color{red}0 \\ \color{blue}0 & \color{blue}{\cos\theta} & \color{blue}{-\sin\theta} & \color{blue}0 \\ \color{green}0 & \color{green}{\sin\theta} & \color{green}{\cos\theta} & \color{green}0 \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix} \begin{pmatrix} x \\ y \\ z \\ 1 \end{pmatrix} = \begin{pmatrix} x \\ \color{blue}{\cos\theta}\cdot y \color{blue}{-\sin\theta}\cdot z \\ \color{green}{\sin\theta}\cdot y + \color{green}{\cos\theta}\cdot z \\ 1 \end{pmatrix}\]

Rotation around Y-axis:
\[\begin{bmatrix} \color{red}{\cos\theta} & \color{red}0 & \color{red}{\sin\theta} & \color{red}0 \\ \color{blue}0 & \color{blue}1 & \color{blue}0 & \color{blue}0 \\ \color{green}{-\sin\theta} & \color{green}0 & \color{green}{\cos\theta} & \color{green}0 \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix} \begin{pmatrix} x \\ y \\ z \\ 1 \end{pmatrix} = \begin{pmatrix} \color{red}{\cos\theta}\cdot x + \color{red}{\sin\theta}\cdot z \\ y \\ \color{green}{-\sin\theta}\cdot x + \color{green}{\cos\theta}\cdot z \\ 1 \end{pmatrix}\]

Rotation around Z-axis:
\[\begin{bmatrix} \color{red}{\cos\theta} & \color{red}{-\sin\theta} & \color{red}0 & \color{red}0 \\ \color{blue}{\sin\theta} & \color{blue}{\cos\theta} & \color{blue}0 & \color{blue}0 \\ \color{green}0 & \color{green}0 & \color{green}1 & \color{green}0 \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix} \begin{pmatrix} x \\ y \\ z \\ 1 \end{pmatrix} = \begin{pmatrix} \color{red}{\cos\theta}\cdot x \color{red}{-\sin\theta}\cdot y \\ \color{blue}{\sin\theta}\cdot x + \color{blue}{\cos\theta}\cdot y \\ z \\ 1 \end{pmatrix}\]

Don't worry about understanding the actual geometry behind this, explaining that is beyond the scope of this guide. What matters is that you have a solid idea of how a rotation is described by a rotation axis and an angle and that you've at least seen what a rotation matrix looks like.

Matrix-Matrix product
========

In the previous section you've seen how transformation matrices can be used to apply transformations to vectors, but this by itself is not very useful. It clearly takes far less effort to do a translation and scaling by hand without all those pesky matrices!

Now, what if I told you that it is possible to combine as many transformations as you want into a single matrix by simply multiplying them? You would be able to apply even the most complex transformations to any vertex with a simple multiplication.

In the same style as the previous section, this is how the product of two 4-by-4 matrices is determined:

\[\begin{bmatrix} \color{red}a & \color{red}b & \color{red}c & \color{red}d \\ \color{blue}e & \color{blue}f & \color{blue}g & \color{blue}h \\ \color{green}i & \color{green}j & \color{green}k & \color{green}l \\ \color{purple}m & \color{purple}n & \color{purple}o & \color{purple}p \end{bmatrix} \begin{bmatrix} \color{red}A & \color{red}B & \color{red}C & \color{red}D \\ \color{blue}E & \color{blue}F & \color{blue}G & \color{blue}H \\ \color{green}I & \color{green}J & \color{green}K & \color{green}L \\ \color{purple}M & \color{purple}N & \color{purple}O & \color{purple}P \end{bmatrix} =\]
\[\begin{bmatrix} \color{red}{aA+bE+cI+dM} & \color{red}{aB+bF+cJ+dN} & \color{red}{aC+bG+cK+dO} & \color{red}{aD+bH+cL+dP} \\ \color{blue}{eA+fE+Ig+hM} & \color{blue}{eB+fF+gJ+hN} & \color{blue}{eC+fG+gK+hO} & \color{blue}{eD+fH+gL+hP} \\ \color{green}{iA+jE+Ik+lM} & \color{green}{iB+jF+kJ+lN} & \color{green}{iC+jG+kK+lO} & \color{green}{iD+jH+kL+lP} \\ \color{purple}{mA+nE+Io+pM} & \color{purple}{mB+nF+oJ+pN} & \color{purple}{mC+nG+oK+pO} & \color{purple}{mD+nH+oL+pP} \end{bmatrix}\]

The above is commonly recognized among mathematicians as an *indecipherable mess*. To get a better idea of what's going on, let's consider two 2-by-2 matrices instead.

\[\begin{bmatrix} \color{red}1 & \color{red}2 \\ \color{blue}3 & \color{blue}4 \end{bmatrix} \begin{bmatrix} \color{green}a & \color{green}b \\ \color{purple}c & \color{purple}d \end{bmatrix} = \begin{bmatrix} \color{red}1\cdot \color{green}a+\color{red}2\cdot \color{purple}c & \color{red}1\cdot \color{green}b + \color{red}2\cdot \color{purple}d \\ \color{blue}3\cdot \color{green}a+\color{blue}4\cdot \color{purple}c & \color{blue}3\cdot \color{green}b+\color{blue}4\cdot \color{purple}d \end{bmatrix}\]

Try to see the pattern here with help of the colors. The factors on the left side (`1,2` and `3,4`) of the multiplication dot are the values in the row of the first matrix. The factors on the right side are the values in the rows of the second matrix repeatedly.

Combining transformations
--------

To demonstrate the multiplication of two matrices, let's try scaling a given vector by `(2,2,2)` and translating it by `(1,2,3)`. Given the translation and scaling matrices above, the following product is calculated:

\[M_{translate}\cdot M_{scale} = \begin{bmatrix} \color{red}1 & \color{red}0 & \color{red}0 & \color{red}1 \\ \color{blue}0 & \color{blue}1 & \color{blue}0 & \color{blue}2 \\ \color{green}0 & \color{green}0 & \color{green}1 & \color{green}3 \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix} \begin{bmatrix} \color{red}{2} & \color{red}0 & \color{red}0 & \color{red}0 \\ \color{blue}0 & \color{blue}{2} & \color{blue}0 & \color{blue}0 \\ \color{green}0 & \color{green}0 & \color{green}{2} & \color{green}0 \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix} = \begin{bmatrix} \color{red}{2} & \color{red}0 & \color{red}0 & \color{red}1 \\ \color{blue}0 & \color{blue}{2} & \color{blue}0 & \color{blue}2 \\ \color{green}0 & \color{green}0 & \color{green}{2} & \color{green}3 \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix}\]

Notice how we want to scale the vector first, but the scale transformation comes last in the multiplication. Pay attention to this when combining transformations or you'll get the opposite of what you've asked for.

Now, let's try to transform a vector and see if it worked:

\[\begin{bmatrix} \color{red}{2} & \color{red}0 & \color{red}0 & \color{red}1 \\ \color{blue}0 & \color{blue}{2} & \color{blue}0 & \color{blue}2 \\ \color{green}0 & \color{green}0 & \color{green}{2} & \color{green}3 \\ \color{purple}0 & \color{purple}0 & \color{purple}0 & \color{purple}1 \end{bmatrix} \begin{pmatrix} x \\ y \\ z \\ 1 \end{pmatrix} = \begin{pmatrix} \color{red}2 x + \color{red}1 \\ \color{blue}2y + \color{blue}2 \\ \color{green}2z + \color{green}3 \\ 1 \end{pmatrix}\]

Perfect! The vector is first scaled by two and then shifted in position by `(1,2,3)`.