var canvas = document.getElementById( "demo_c2_uniforms" ).getElementsByTagName( "canvas" )[0];
var gl = canvas.getContext( "experimental-webgl" );

// Populate vertex buffer
var vertices = [
	0.0, 0.5,
	0.5, -0.5,
	-0.5, -0.5
];
var vbo = gl.createBuffer();
gl.bindBuffer( gl.ARRAY_BUFFER, vbo );
gl.bufferData( gl.ARRAY_BUFFER, new Float32Array( vertices ), gl.STATIC_DRAW );

// Load shaders
var vertexShader = gl.createShader( gl.VERTEX_SHADER );
gl.shaderSource( vertexShader, "attribute vec2 position; void main() { gl_Position = vec4( position, 0.0, 1.0 ); }" );
gl.compileShader( vertexShader );
console.log( gl.getShaderInfoLog( vertexShader ) );

var fragmentShader = gl.createShader( gl.FRAGMENT_SHADER );
gl.shaderSource( fragmentShader, "precision mediump float; uniform vec3 triangleColor; void main() { gl_FragColor = vec4( triangleColor, 1.0 ); }" );
gl.compileShader( fragmentShader );
console.log( gl.getShaderInfoLog( fragmentShader ) );

var shaderProgram = gl.createProgram();
gl.attachShader( shaderProgram, vertexShader );
gl.attachShader( shaderProgram, fragmentShader );
gl.linkProgram( shaderProgram );
gl.useProgram( shaderProgram );

var posAttrib = gl.getAttribLocation( shaderProgram, "position" );
gl.enableVertexAttribArray( posAttrib );
gl.vertexAttribPointer( posAttrib, 2, gl.FLOAT, gl.FALSE, 0, 0 );

// Draw
var uniColor = gl.getUniformLocation( shaderProgram, "triangleColor" );
var timeOffset = +new Date();

var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame;
function frame()
{
	if ( !elementInViewport( canvas ) ) { requestAnimationFrame( frame ); return; }

	gl.clearColor( 0.0, 0.0, 0.0, 1.0 );
	gl.clear( gl.COLOR_BUFFER_BIT );

	gl.uniform3f( uniColor, ( Math.sin( +new Date() / 1000.0 * 4.0 ) + 1.0 ) / 2.0, 0.0, 0.0 );

	gl.drawArrays( gl.TRIANGLES, 0, 3 );

	requestAnimationFrame( frame );
}
requestAnimationFrame( frame );