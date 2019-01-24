#!/bin/sh

pandoc \
    -s \
    ebook/title.md \
    content/articles-en/introduction.md \
    content/articles-en/context.md \
    content/articles-en/drawing.md \
    content/articles-en/textures.md \
    content/articles-en/transformations.md \
    content/articles-en/depthstencils.md \
    content/articles-en/framebuffers.md \
    content/articles-en/geometry.md \
    content/articles-en/feedback.md \
    --highlight-style=zenburn \
    --indented-code-classes=cpp \
    --toc \
    -o "ebook/Modern OpenGL Guide.pdf"

pandoc \
    -s \
    ebook/title.md \
    content/articles-en/introduction.md \
    content/articles-en/context.md \
    content/articles-en/drawing.md \
    content/articles-en/textures.md \
    content/articles-en/transformations.md \
    content/articles-en/depthstencils.md \
    content/articles-en/framebuffers.md \
    content/articles-en/geometry.md \
    content/articles-en/feedback.md \
    --highlight-style=zenburn \
    --indented-code-classes=cpp \
    --toc \
    --webtex="https://latex.codecogs.com/png.latex?" \
    -o "ebook/Modern OpenGL Guide.epub"
