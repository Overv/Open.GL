#version 150 core

in vec3 Color;

out vec4 outColor;

void main()
{
    outColor = vec4(1.0 - Color.r, 1.0 - Color.g, 1.0 - Color.b, 1.0);
    // or
    outColor = vec4(1.0 - Color.x, 1.0 - Color.y, 1.0 - Color.z, 1.0);
    // or even
    outColor = vec4(1.0 - Color, 1.0);
}