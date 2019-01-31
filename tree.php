<?php
//Array of morse combinations
//-1 is a dah, +1 is a dit and 0 is nothing
$chars = array(
		array('K', -1, 1, -1, 0, 0, 0), 
		array('M', -1, -1, 0, 0, 0, 0), 
		array('R', 1, -1, 1, 0, 0, 0), 
		array('S', 1, 1, 1, 0, 0, 0), 
		array('U', 1, 1, -1, 0, 0, 0), 
		array('A', 1, -1, 0, 0, 0, 0), 
		array('P', 1, -1, -1, 1, 0, 0), 
		array('T', -1, 0, 0, 0, 0, 0), 
		array('L', 1, -1, 1, 1, 0, 0), 
		array('O', -1, -1, -1, 0, 0, 0), 
		array('W', 1, -1, -1, 0, 0, 0), 
		array('I', 1, 1, 0, 0, 0, 0), 
		array('.', 1, -1, 1, -1, 1, -1), 
		array('N', -1, 1, 0, 0, 0, 0), 
		array('J', 1, -1, -1, -1, 0, 0), 
		array('E', 1, 0, 0, 0, 0, 0), 
		array('F', 1, 1, -1, 1, 0, 0), 
		array('0', -1, -1, -1, -1, -1, 0), 
		array('Y', -1, 1, -1, -1, 0, 0), 
		array(',', -1, -1, 1, 1, -1, -1), 
		array('V', 1, 1, 1, -1, 0, 0 ), 
		array('G', -1, -1, 1, 0, 0, 0), 
		array('5', 1, 1, 1, 1, 1, 0), 
		array('/', -1, 1, 1, -1, 1, 0), 
		array('Q', -1, -1, 1, -1, 0, 0), 
		array('9', -1, -1, -1, -1, 1, 0), 
		array('Z', -1, -1, 1, 1, 0, 0), 
		array('H', 1, 1, 1, 1, 0, 0), 
		array('3', 1, 1, 1, -1, -1, 0), 
		array('8', -1, -1, -1, 1, 1, 0), 
		array('B', -1, 1, 1, 1, 0, 0), 
		array('?', 1, 1, -1, -1, 1, 1), 
		array('4', 1, 1, 1, 1, -1, 0), 
		array('2', 1, 1, -1, -1, -1, 0), 
		array('7', -1, -1, 1, 1, 1, 0), 
		array('C', -1, 1, -1, 1, 0, 0), 
		array('1', 1, -1, -1, -1, -1, 0), 
		array('D', -1, 1, 1, 0, 0, 0), 
		array('6', -1, 1, 1, 1, 1, 0), 
		array('X', -1, 1, 1, -1, 0, 0)
		);


		//Builds a binary search tree from the array. 
		function buildTree() {
			$searchTree = array_fill(0,127,-2);

			for ($i = 0; $i < 40; $i++) {
				//now we just add each character to the tree. I think that should be simple.

				$index = 0;
				for ($j = 1; $j < 7; $j++) { //iterate over the (up to) six dits and dahs
					//dahs (<0) go left dits (>0) go right
					if ($GLOBALS['chars'][$i][$j] < 0) { //if we have a dah
						//go to the node to the left
						$index = 2 * $index +1;
						if($j == 6)
							$searchTree[$index] = $GLOBALS['chars'][$i][0];
					} else if ($GLOBALS['chars'][$i][$j] > 0) {
						$index = 2 * $index + 2;
						if($j == 6)
							$searchTree[$index] = $GLOBALS['chars'][$i][0];
					} else { // fill in the charcter pointed to by i in the search tree
						$searchTree[$index] = $GLOBALS['chars'][$i][0];
						break;
					}
				}
			}
			return $searchTree;
		}

//Determines if a node has any children with a value
function anyChildren($tree,$index) {
	if($index > 127)
		return false;
	if($tree[$index] != -2)
		return true;
	else if(anyChildren($tree,2*$index+1) || anyChildren($tree,2*$index+2))
		return true;
	return false;
}

function drawTree($img, $tree, $startX, $startY, $spread, $drop,$line_colour,$clear_colour) {

	drawNode($img,$tree,1,$startX,$startY,1,$spread,$drop,$line_colour,$clear_colour,true);
	drawNode($img,$tree,2,$startX,$startY,1,$spread,$drop,$line_colour,$clear_colour,false);
	imagefilledellipse($img,$startX,$startY,50,50,$clear_colour);
	imageellipse($img,$startX,$startY,50,50,$line_colour);
	imagefttext($img,10,0,$startX-18,$startY+6,$line_colour,'./LiberationMono-Bold.ttf','Start');

}

function drawNode($img,$tree,$index,$parentX,$parentY,$layer,$spread,$drop,$line_colour,$clear_colour,$left) {
	if ($index > 126) //ensure we haven't ran past the array
		return;

	if($tree[$index] == -2) {  //if this node is empty, make sure it has children before drawing
		if($layer == 6 || anyChildren($tree,$index) == false)
			return;
	}

	//determine our x coordinate
	if ($left)
		$x = $parentX - $spread;
	else 
		$x = $parentX + $spread;

	//recurse first, so we can draw over any lines drawn
	drawNode($img,$tree,2*$index+1, $x, $parentY+$drop, $layer+1, $spread/2, $drop,$line_colour,$clear_colour, true);
	drawNode($img,$tree,2*$index+2, $x, $parentY+$drop, $layer+1, $spread/2, $drop, $line_colour,$clear_colour,false);

	//set font size a little larger for small symbols
	if($tree[$index] == "." || $tree[$index] == ',') $fontSize = 14; 
	else $fontSize = 12;

	if ($left) { //spread to the left 
		imageline($img,$parentX,$parentY,$x,$parentY + $drop,$line_colour);
		imagefilledellipse($img,$x, $parentY + $drop, 30, 30,$clear_colour);
		imageellipse($img,$x, $parentY + $drop, 30, 30,$line_colour);
		if ($tree[$index] != -2) //Only print if its a valid symbol
			imagefttext($img,$fontSize,0, $x-6, $parentY+$drop+6,$line_colour,'./LiberationMono-Bold.ttf',$tree[$index]);
	} else {
		imageline($img,$parentX,$parentY,$x,$parentY + $drop,$line_colour);
		imagefilledellipse($img,$x, $parentY + $drop, 30, 30,$clear_colour);
		imageellipse($img,$x, $parentY + $drop, 30, 30,$line_colour);
		if ($tree[$index] != -2)  //only print if its a valid symbol
			imagefttext($img,$fontSize,0, $x-6, $parentY+$drop+6,$line_colour,'./LiberationMono-Bold.ttf',$tree[$index]);
	}


}



imagesetthickness($img,10);
$tree = buildTree();
$my_img = imagecreate(1300,375);
$background = imagecolorallocate( $my_img, 255, 255, 255 );
$text_colour = imagecolorallocate( $my_img, 255, 255, 0 );
$line_colour = imagecolorallocate( $my_img, 0, 0, 0 );

drawTree($my_img,$tree,650,50,300,50,$line_colour);


header( "Content-type: image/png" );
imagepng( $my_img );
imagecolordeallocate( $line_color );
imagecolordeallocate( $text_color );
imagecolordeallocate( $background );
imagedestroy( $my_img );

?> 
