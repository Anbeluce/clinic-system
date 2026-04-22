( function( window, document ) {
  function medicare_clinic_keepFocusInMenu() {
    document.addEventListener( 'keydown', function( e ) {
      const medicare_clinic_nav = document.querySelector( '.sidenav' );
      if ( ! medicare_clinic_nav || ! medicare_clinic_nav.classList.contains( 'open' ) ) {
        return;
      }
      const elements = [...medicare_clinic_nav.querySelectorAll( 'input, a, button' )],
        medicare_clinic_lastEl = elements[ elements.length - 1 ],
        medicare_clinic_firstEl = elements[0],
        medicare_clinic_activeEl = document.activeElement,
        tabKey = e.keyCode === 9,
        shiftKey = e.shiftKey;
      if ( ! shiftKey && tabKey && medicare_clinic_lastEl === medicare_clinic_activeEl ) {
        e.preventDefault();
        medicare_clinic_firstEl.focus();
      }
      if ( shiftKey && tabKey && medicare_clinic_firstEl === medicare_clinic_activeEl ) {
        e.preventDefault();
        medicare_clinic_lastEl.focus();
      }
    } );
  }
  medicare_clinic_keepFocusInMenu();
} )( window, document );