<?php
/**
 * Typography control class.
 *
 * @since  1.0.0
 * @access public
 */

class VW_Hospital_Lite_Control_Typography extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'typography';

	/**
	 * Array 
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $l10n = array();

	/**
	 * Set up our control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $id
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $id, $args = array() ) {

		// Let the parent class do its thing.
		parent::__construct( $manager, $id, $args );

		// Make sure we have labels.
		$this->l10n = wp_parse_args(
			$this->l10n,
			array(
				'color'       => esc_html__( 'Font Color', 'vw-hospital-lite' ),
				'family'      => esc_html__( 'Font Family', 'vw-hospital-lite' ),
				'size'        => esc_html__( 'Font Size',   'vw-hospital-lite' ),
				'weight'      => esc_html__( 'Font Weight', 'vw-hospital-lite' ),
				'style'       => esc_html__( 'Font Style',  'vw-hospital-lite' ),
				'line_height' => esc_html__( 'Line Height', 'vw-hospital-lite' ),
				'letter_spacing' => esc_html__( 'Letter Spacing', 'vw-hospital-lite' ),
			)
		);
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'vw-hospital-lite-ctypo-customize-controls' );
		wp_enqueue_style(  'vw-hospital-lite-ctypo-customize-controls' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		// Loop through each of the settings and set up the data for it.
		foreach ( $this->settings as $setting_key => $setting_id ) {

			$this->json[ $setting_key ] = array(
				'link'  => $this->get_link( $setting_key ),
				'value' => $this->value( $setting_key ),
				'label' => isset( $this->l10n[ $setting_key ] ) ? $this->l10n[ $setting_key ] : ''
			);

			if ( 'family' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_families();

			elseif ( 'weight' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_weight_choices();

			elseif ( 'style' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_style_choices();
		}
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<ul>

		<# if ( data.family && data.family.choices ) { #>

			<li class="typography-font-family">

				<# if ( data.family.label ) { #>
					<span class="customize-control-title">{{ data.family.label }}</span>
				<# } #>

				<select {{{ data.family.link }}}>

					<# _.each( data.family.choices, function( label, choice ) { #>
						<option value="{{ choice }}" <# if ( choice === data.family.value ) { #> selected="selected" <# } #>>{{ label }}</option>
					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.weight && data.weight.choices ) { #>

			<li class="typography-font-weight">

				<# if ( data.weight.label ) { #>
					<span class="customize-control-title">{{ data.weight.label }}</span>
				<# } #>

				<select {{{ data.weight.link }}}>

					<# _.each( data.weight.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.style && data.style.choices ) { #>

			<li class="typography-font-style">

				<# if ( data.style.label ) { #>
					<span class="customize-control-title">{{ data.style.label }}</span>
				<# } #>

				<select {{{ data.style.link }}}>

					<# _.each( data.style.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.size ) { #>

			<li class="typography-font-size">

				<# if ( data.size.label ) { #>
					<span class="customize-control-title">{{ data.size.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.size.link }}} value="{{ data.size.value }}" />

			</li>
		<# } #>

		<# if ( data.line_height ) { #>

			<li class="typography-line-height">

				<# if ( data.line_height.label ) { #>
					<span class="customize-control-title">{{ data.line_height.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.line_height.link }}} value="{{ data.line_height.value }}" />

			</li>
		<# } #>

		<# if ( data.letter_spacing ) { #>

			<li class="typography-letter-spacing">

				<# if ( data.letter_spacing.label ) { #>
					<span class="customize-control-title">{{ data.letter_spacing.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.letter_spacing.link }}} value="{{ data.letter_spacing.value }}" />

			</li>
		<# } #>

		</ul>
	<?php }

	/**
	 * Returns the available fonts.  Fonts should have available weights, styles, and subsets.
	 *
	 * @todo Integrate with Google fonts.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_fonts() { return array(); }

	/**
	 * Returns the available font families.
	 *
	 * @todo Pull families from `get_fonts()`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	function get_font_families() {

		return array(
			'' => __( 'No Fonts', 'vw-hospital-lite' ),
        'Abril Fatface' => __( 'Abril Fatface', 'vw-hospital-lite' ),
        'Acme' => __( 'Acme', 'vw-hospital-lite' ),
        'Anton' => __( 'Anton', 'vw-hospital-lite' ),
        'Architects Daughter' => __( 'Architects Daughter', 'vw-hospital-lite' ),
        'Arimo' => __( 'Arimo', 'vw-hospital-lite' ),
        'Arsenal' => __( 'Arsenal', 'vw-hospital-lite' ),
        'Arvo' => __( 'Arvo', 'vw-hospital-lite' ),
        'Alegreya' => __( 'Alegreya', 'vw-hospital-lite' ),
        'Alfa Slab One' => __( 'Alfa Slab One', 'vw-hospital-lite' ),
        'Averia Serif Libre' => __( 'Averia Serif Libre', 'vw-hospital-lite' ),
        'Bangers' => __( 'Bangers', 'vw-hospital-lite' ),
        'Boogaloo' => __( 'Boogaloo', 'vw-hospital-lite' ),
        'Bad Script' => __( 'Bad Script', 'vw-hospital-lite' ),
        'Bitter' => __( 'Bitter', 'vw-hospital-lite' ),
        'Bree Serif' => __( 'Bree Serif', 'vw-hospital-lite' ),
        'BenchNine' => __( 'BenchNine', 'vw-hospital-lite' ),
        'Cabin' => __( 'Cabin', 'vw-hospital-lite' ),
        'Cardo' => __( 'Cardo', 'vw-hospital-lite' ),
        'Courgette' => __( 'Courgette', 'vw-hospital-lite' ),
        'Cherry Swash' => __( 'Cherry Swash', 'vw-hospital-lite' ),
        'Cormorant Garamond' => __( 'Cormorant Garamond', 'vw-hospital-lite' ),
        'Crimson Text' => __( 'Crimson Text', 'vw-hospital-lite' ),
        'Cuprum' => __( 'Cuprum', 'vw-hospital-lite' ),
        'Cookie' => __( 'Cookie', 'vw-hospital-lite' ),
        'Chewy' => __( 'Chewy', 'vw-hospital-lite' ),
        'Days One' => __( 'Days One', 'vw-hospital-lite' ),
        'Dosis' => __( 'Dosis', 'vw-hospital-lite' ),
        'Droid Sans' => __( 'Droid Sans', 'vw-hospital-lite' ),
        'Economica' => __( 'Economica', 'vw-hospital-lite' ),
        'Fredoka One' => __( 'Fredoka One', 'vw-hospital-lite' ),
        'Fjalla One' => __( 'Fjalla One', 'vw-hospital-lite' ),
        'Francois One' => __( 'Francois One', 'vw-hospital-lite' ),
        'Frank Ruhl Libre' => __( 'Frank Ruhl Libre', 'vw-hospital-lite' ),
        'Gloria Hallelujah' => __( 'Gloria Hallelujah', 'vw-hospital-lite' ),
        'Great Vibes' => __( 'Great Vibes', 'vw-hospital-lite' ),
        'Handlee' => __( 'Handlee', 'vw-hospital-lite' ),
        'Hammersmith One' => __( 'Hammersmith One', 'vw-hospital-lite' ),
        'Inconsolata' => __( 'Inconsolata', 'vw-hospital-lite' ),
        'Indie Flower' => __( 'Indie Flower', 'vw-hospital-lite' ),
        'IM Fell English SC' => __( 'IM Fell English SC', 'vw-hospital-lite' ),
        'Julius Sans One' => __( 'Julius Sans One', 'vw-hospital-lite' ),
        'Josefin Slab' => __( 'Josefin Slab', 'vw-hospital-lite' ),
        'Josefin Sans' => __( 'Josefin Sans', 'vw-hospital-lite' ),
        'Kanit' => __( 'Kanit', 'vw-hospital-lite' ),
        'Lobster' => __( 'Lobster', 'vw-hospital-lite' ),
        'Lato' => __( 'Lato', 'vw-hospital-lite' ),
        'Lora' => __( 'Lora', 'vw-hospital-lite' ),
        'Libre Baskerville' => __( 'Libre Baskerville', 'vw-hospital-lite' ),
        'Lobster Two' => __( 'Lobster Two', 'vw-hospital-lite' ),
        'Merriweather' => __( 'Merriweather', 'vw-hospital-lite' ),
        'Monda' => __( 'Monda', 'vw-hospital-lite' ),
        'Montserrat' => __( 'Montserrat', 'vw-hospital-lite' ),
        'Muli' => __( 'Muli', 'vw-hospital-lite' ),
        'Marck Script' => __( 'Marck Script', 'vw-hospital-lite' ),
        'Noto Serif' => __( 'Noto Serif', 'vw-hospital-lite' ),
        'Open Sans' => __( 'Open Sans', 'vw-hospital-lite' ),
        'Overpass' => __( 'Overpass', 'vw-hospital-lite' ),
        'Overpass Mono' => __( 'Overpass Mono', 'vw-hospital-lite' ),
        'Oxygen' => __( 'Oxygen', 'vw-hospital-lite' ),
        'Orbitron' => __( 'Orbitron', 'vw-hospital-lite' ),
        'Patua One' => __( 'Patua One', 'vw-hospital-lite' ),
        'Pacifico' => __( 'Pacifico', 'vw-hospital-lite' ),
        'Padauk' => __( 'Padauk', 'vw-hospital-lite' ),
        'Playball' => __( 'Playball', 'vw-hospital-lite' ),
        'Playfair Display' => __( 'Playfair Display', 'vw-hospital-lite' ),
        'PT Sans' => __( 'PT Sans', 'vw-hospital-lite' ),
        'Philosopher' => __( 'Philosopher', 'vw-hospital-lite' ),
        'Permanent Marker' => __( 'Permanent Marker', 'vw-hospital-lite' ),
        'Poiret One' => __( 'Poiret One', 'vw-hospital-lite' ),
        'Quicksand' => __( 'Quicksand', 'vw-hospital-lite' ),
        'Quattrocento Sans' => __( 'Quattrocento Sans', 'vw-hospital-lite' ),
        'Raleway' => __( 'Raleway', 'vw-hospital-lite' ),
        'Rubik' => __( 'Rubik', 'vw-hospital-lite' ),
        'Rokkitt' => __( 'Rokkitt', 'vw-hospital-lite' ),
        'Russo One' => __( 'Russo One', 'vw-hospital-lite' ),
        'Righteous' => __( 'Righteous', 'vw-hospital-lite' ),
        'Slabo' => __( 'Slabo', 'vw-hospital-lite' ),
        'Source Sans Pro' => __( 'Source Sans Pro', 'vw-hospital-lite' ),
        'Shadows Into Light Two' => __( 'Shadows Into Light Two', 'vw-hospital-lite'),
        'Shadows Into Light' => __( 'Shadows Into Light', 'vw-hospital-lite' ),
        'Sacramento' => __( 'Sacramento', 'vw-hospital-lite' ),
        'Shrikhand' => __( 'Shrikhand', 'vw-hospital-lite' ),
        'Tangerine' => __( 'Tangerine', 'vw-hospital-lite' ),
        'Ubuntu' => __( 'Ubuntu', 'vw-hospital-lite' ),
        'VT323' => __( 'VT323', 'vw-hospital-lite' ),
        'Varela Round' => __( 'Varela Round', 'vw-hospital-lite' ),
        'Vampiro One' => __( 'Vampiro One', 'vw-hospital-lite' ),
        'Vollkorn' => __( 'Vollkorn', 'vw-hospital-lite' ),
        'Volkhov' => __( 'Volkhov', 'vw-hospital-lite' ),
        'Yanone Kaffeesatz' => __( 'Yanone Kaffeesatz', 'vw-hospital-lite' )
		);
	}

	/**
	 * Returns the available font weights.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_weight_choices() {

		return array(
			'' => esc_html__( 'No Fonts weight', 'vw-hospital-lite' ),
			'100' => esc_html__( 'Thin',       'vw-hospital-lite' ),
			'300' => esc_html__( 'Light',      'vw-hospital-lite' ),
			'400' => esc_html__( 'Normal',     'vw-hospital-lite' ),
			'500' => esc_html__( 'Medium',     'vw-hospital-lite' ),
			'700' => esc_html__( 'Bold',       'vw-hospital-lite' ),
			'900' => esc_html__( 'Ultra Bold', 'vw-hospital-lite' ),
		);
	}

	/**
	 * Returns the available font styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_style_choices() {

		return array(
			'normal'  => esc_html__( 'Normal', 'vw-hospital-lite' ),
			'italic'  => esc_html__( 'Italic', 'vw-hospital-lite' ),
			'oblique' => esc_html__( 'Oblique', 'vw-hospital-lite' )
		);
	}
}
