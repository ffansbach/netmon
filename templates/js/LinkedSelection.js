/**
 * LinkedSelection ist ein Klasse zur Steuerung dynamisch verketteter Auswahllisten
 * @param inputSelects ein Array mit den IDs der Auswahllisten in hierarchischer Reihenfolge
 *						Bsp: [ 'select1', 'select2', 'select3' ]
 * @param callback Funktion, welche beim Abschließen (und Ändern) der Auswahl aufgerufen werden soll
 * @param data das Daten-Objekt in JSON
 *						Bsp: { 'select1':['wert1','text1'], 'select2':['wert5','text5'] }
 **/
function LinkedSelection( inputSelects, data )
{
	var self = this;				/* um aus EventHandlern auf diese Instanz zugreifen zu können */
	var selects = new Array();		/* Liste der verketteten Auswahllisten */

	/**
	 * Die Funktion changeHandler wird dem onchange-Handler jeder Auswahlliste zugewiesen.
	 * Wenn eine gültige Auswahl getroffen wurde, soll entweder die als nächste
	 * Auswahlliste (nextSelect) bekannte Auswahlliste mit Daten befüllt werden,
	 * oder die Callback-Funktion ausgeführt werden.
	 **/
	var changeHandler = function()
	{
		var value = this.selectedValue();


if (isNaN(document.getElementById('discription'))) {
  if(value=='service') {
    document.getElementById('discription').style.display = 'block';
  } else if (value=='false' || value=='node' || value=='vpn' || value=='client') {
    document.getElementById('discription').style.display = 'none';
  }
}


		//Eingabefeld für den port zeigen wenn die Port-option ausgewählt wurde
		if( value == 'port' )
		{
			document.getElementById('portInput').style.visibility='visible';
		} else
		{
    		document.getElementById('portInput').style.visibility='hidden';
		}

		// Auf die nächste Auswahlliste folgende Auswahllisten müssen wieder
		// in den default-Zustand versetzt werden
		if( typeof(this.nextSelect) == 'object' )
		{
			for( var i = this.nextSelect.selectID + 1; i < selects.length; i++ )
				selects[i].replaceOptions( new Array() );
		}

		// Abbrechen, wenn ein Dummy-Wert ausgewählt wurde
		if( value == '--' )
		{
			if( this.selectID < selects.length )
				selects[ this.selectID +1 ].replaceOptions( new Array() );

			return;
		}

		if( typeof(this.nextSelect) == 'object' )
		{
			/*
			 * nextSelect ist eine Auswahlliste
			 */

			// Wenn keine Daten zur gemachten Auswahl zur Verfügung stehen,
			// müssen wir sicherstellen, dass wir auf keine nicht vorhandenen Objekte zugreifen.
			if( !data[ this.nextSelect.id ][ value ] )
			{
				if( !data[ this.nextSelect.id ] )
					data[ this.nextSelect.id ] = {};

				data[ this.nextSelect.id ][ value ] = new Array();
			}

			// Neue Optionen in der nächsten Auswahlliste setzen
			this.nextSelect.replaceOptions( data[ this.nextSelect.id ][ value ] );
		}
	};

	/**
	 * replaceOptions ersetzt die aktuellen Optionen der Auswahlliste durch
	 * die im Array newOptions gelieferten Daten. Wenn ein leeres Array übergeben
	 * wird, wird die default-Option "--" gesetzt.
	 * @param newOptions ein Array mit den neuen Optionen
	 *					  Bsp: [ ['value1','text1'], ['value2','text2'], ]
	 **/
	var replaceOptions = function( newOptions )
	{
		/*
		 * Diese Funktion setzt bewusst DOM-Methoden ein und verzichtet
		 * auf die vom Options-Objekt gegebenen Möglichkeiten.
		 */

		// alte Optionen der Auswahlliste löschen
		var opts = this.getElementsByTagName( 'option' );
		while( opts.length > 0 )
			this.removeChild( opts[0] );

		// wenn keine neuen Optionen übergeben wurden, default-Option setzen
		// andernfalls "Bitte wählen" voranstellen
		if( newOptions.length == 0)
			this.addOption( 'false', 'erst Service auswählen' );
		else
			this.addOption( 'false', 'Bitte wählen:' );

		// neue Optionen in die Auswahlliste schreiben
		for( var i = 0; i < newOptions.length; i++ )
			this.addOption( newOptions[i][0], newOptions[i][1] );
	};

	/*
	 * Fügt der Auswahlliste eine neue Option hinzu
	 * @param value Wert der neuen Option
	 * @param text Name der neuen Option
	 */
	var addOption = function( value, text )
	{
		var opt = document.createElement( 'option' );
		opt.value = value;
		opt.appendChild( document.createTextNode( text ) );
		this.appendChild( opt );
	};

	/**
	 * holt den Wert der aktuell gewählten Option
	 * @returns den Value der aktuell gewählten Option
	 **/
	var selectedValue = function()
	{
		return this.options[ this.selectedIndex ].value;
	};

	/**
	 * holt den Text (Name) der aktuell gewählten Option
	 * @returns den Text der aktuell gewählten Option
	 **/
	var selectedText = function()
	{
		return this.options[ this.selectedIndex ].text;
	};

	/**
	 * Selektiere die Option mit dem Wert value, wenn keine Option mit dem Wert
	 * value existiert, wird die Auswahl nicht geändert.
	 * @param value der Wert den eine Option haben muss, um ausgewählt zu werden.
	 **/
	var selectByValue = function( value )
	{
		for( var i = 0; i < this.options.length; i++ )
		{
			if( this.options[i].value == value )
				this.selectedIndex = i;
		}
	}

	/**
	 * Initialisiere den Manager für verkettete Auswahllisten.
	 * Findet Auswahllisten anhand der (per inputSelects) bekannten IDs.
	 * Bestückt die Auswahllisten mit den nötigen Funktionen und Event-Handlern
	 **/
	this.init = function()
	{
		// bestücke bestehende selects
		for( var i = 0; i < inputSelects.length; i++ )
		{
			var t = document.getElementById( inputSelects[i] );

			// ignoriere falsche IDs
			if(!t)
				continue;

			// neue Funktionen und Event-Handler zuweisen und in selects registrieren
			t.replaceOptions = replaceOptions;
			t.addOption = addOption;
			t.selectedValue = selectedValue;
			t.selectedText = selectedText;
			t.selectByValue = selectByValue;
			t.selectID = selects.length;
			t.onchange = changeHandler;
			selects.push( t );

			// registriere Auswahlliste als nextSelect bei der vorhergehenden
			if( selects.length > 1 )
				selects[ selects.length-2 ].nextSelect = t;
		}
	};

	// initialisieren!
	this.init();
}