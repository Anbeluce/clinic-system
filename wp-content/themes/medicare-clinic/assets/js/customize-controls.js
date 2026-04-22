( function( api ) {

	// Extends our custom "medicare-clinic" section.
	api.sectionConstructor['medicare-clinic'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );