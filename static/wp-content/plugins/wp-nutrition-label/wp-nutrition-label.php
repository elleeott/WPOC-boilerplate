<?php
/*
Plugin Name: wp-nutrition-label
Plugin URI: http://romkey.com/code/wp-nutrition-label
Description:  Provides a Wordpress shortcode which generate an FDA-style nutrition label.
Text Domain: wp-nutrition-label
Domain Path: /languages
Author: John Romkey
Version: 0.4
Author URI: http://romkey.com/
*/

add_shortcode('nutr-label', 'nutr_label_shortcode');

/*
 * output our style sheet at the head of the file
 * because it's brief, we just embed it rather than force an extra http fetch
 *
 * @return void
 */

/* attributes we look for:
 *    servingsize, servings, calories, totalfat, satfat, transfat, cholestrol, sodium, carbohydrates, fiber, sugars, protein
 * also,
 *    id, class
 *
 * @param array $atts
 * @return string
 */
function nutr_label_shortcode($atts) {
	$args = shortcode_atts( array(
	'servingsize' => 0,
	'servings' => 0,
	'calories' => 0,
	'totalfat' => 0,
	'satfat' => 0,
	'transfat' => 0,
	'cholesterol' => 0,
	'sodium' => 0,
	'carbohydrates' => 0,
	'fiber' => 0,
	'sugars' => 0,
	'protein' => 0,
	'vitamin_a' => 0,
	'viamin_c' => 0,
	'width' => 22,
	'id' => '',
	'cssclass' => '' ), $atts );
	return nutr_label_generate($args);
}

/*
 * @param integer $contains
 * @param integer $reference
 * @return integer
 */
function nutr_percentage($contains, $reference) {
  return intval($contains/$reference*100);
}

/*
 * @param array $args
 * @return string
 */
function nutr_label_generate($args) {
  extract($args, EXTR_PREFIX_ALL, 'nutr');
  if($nutr_calories == 0) {
    $nutr_calories = (($nutr_protein + $nutr_carbohydrates)*4) + ($nutr_totalfat * 9);
  }

  $rda = array( 'totalfat' => 65,
		   'satfat' => 20,
		   'cholesterol' => 300,
		   'sodium' => 2300,
		   'carbohydrates' => 300,
		   'fiber' => 25,
		   'protein' => 50,
		   'vitamin_a' => 5000,
		   'vitamin_c' => 60,
		   'calcium' => 1000,
		   'iron' => 18 );

  /* attempt to restyle the label */
  $style = '';

	return '
	<div class="nutrition-label">
		<h2>Nutrition Facts</h2>
		<table>
			<thead>
				<tr>
					<th colspan="2">Serving Size: '.$nutr_servingsize.'</td>
				</tr>
				<tr>
					<th class="servings" cospan="2">Number of Servings: '.$nutr_servings.'</th>
				</tr>
			</thead>
		</table>
		<table>
			<tbody>
				<tr>
					<th colspan="2">Amount Per Serving</th>
				</tr>
				<tr>
					<th class="calories"><strong>Calories</strong> '.$nutr_calories.'</th>
					<td class="calories">Calories from Fat '.($nutr_totalfat * 9).'</td>
				</tr>
				<tr>
					<td colspan="2" class="percent-value">% Daily Value</td>				
				</tr>
		</table>
		<table>
			<tbody>
				<tr>
					<th><strong>Total Fat</strong> '.$nutr_totalfat.'</th>
					<td>'.nutr_percentage($nutr_totalfat, $rda['totalfat']).'%</td>
				</tr>
				<tr>
					<th class="indent">Saturated Fat '.$nutr_satfat.'g</th>
					<td>'.nutr_percentage($nutr_satfat, $rda['satfat']).'%</td>
				</tr>
				<tr>
					<th class="indent">Trans Fat '.$nutr_transfat.'g</th>
					<td></td>
				</tr>
				<tr>
					<th><strong>Cholesterol</strong> '.$nutr_cholesterol.'mg</th>
					<td>'.nutr_percentage($nutr_cholesterol, $rda['cholesterol']).'%</td>
				</tr>
				<tr>
					<th><strong>Sodium</strong> '.$nutr_sodium.'mg</th>
					<td>'.nutr_percentage($nutr_sodium, $rda['sodium']).'%</td>
				</tr>
				<tr>
					<th><strong>Total Carbohydrate</strong> '.$nutr_carbohydrates.'g</th>
					<td>'.nutr_percentage($nutr_carbohydrates, $rda['carbohydrates']).'%</td>
				</tr>
				<tr>
					<th class="indent">Dietary Fiber '.$nutr_fiber.'g</th>
					<td>'.nutr_percentage($nutr_fiber, $rda['fiber']).'%</td>
				</tr>
				<tr>
					<th class="indent">Sugars '.$nutr_sugars.'g</th>
					<td></td>
				</tr>
				<tr>
					<th class="protein"><strong>Protein</strong> '.$nutr_protein.'g</th>
					<td class="protein">'.nutr_percentage($nutr_fiber, $rda['fiber']).'%</td>
				</tr>
				<tr>
					<th colspan="2" class="footnote">*Percent Daily Values are based on a 2,000 calorie diet. Your daily values may be higher or lower depending on your calorie needs.</th>
				</tr>
			</tbody>
		</table>
		
	</div>';
} ?>