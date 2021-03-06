<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_radio' ) ) :
/**
 * Defines the radio field type.
 * 
 * @package			AdminPageFramework
 * @subpackage		FieldType
 * @since			2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_radio extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'radio' );
	
	/**
	 * Defines the default key-values of this field type. 
	 */
	protected $aDefaultKeys = array(
		'label'			=> array(),
		'attributes'	=> array(
		),
	);

	/**
	 * Loads the field type necessary components.
	 */ 
	public function _replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function _replyToGetStyles() {
		return "/* Radio Field Type */
			.admin-page-framework-field input[type='radio'] {
				margin-right: 0.5em;
			}		
			.admin-page-framework-field-radio .admin-page-framework-input-label-container {
				padding-right: 1em;
			}			
			.admin-page-framework-field-radio .admin-page-framework-input-container {
				display: inline;
			}			
		";
	}

	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function _replyToGetScripts() {

		/*	The below JavaScript function will be triggered when a new repeatable field is added. Since the APF repeater script does not
			renew the color piker element (while it does on the input tag value), the renewal task must be dealt here separately. */	
		$aJSArray = json_encode( $this->aFieldTypeSlugs );
		return "			
			jQuery( document ).ready( function(){
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( nodeField, sFieldType, sFieldTagID ) {
			
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
													
						/* the checked state of radio buttons somehow lose their values so re-check them again */	
						nodeField.closest( '.admin-page-framework-fields' )
							.find( 'input[type=radio][checked=checked]' )
							.attr( 'checked', 'checked' );
							
						/* Rebind the checked attribute updater */
						nodeField.find( 'input[type=radio]' ).change( function() {
							jQuery( this ).closest( '.admin-page-framework-field' )
								.find( 'input[type=radio]' )
								.attr( 'checked', false );
							jQuery( this ).attr( 'checked', 'Checked' );
						});

					}
				});
			});
		";				
	}		
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Removed unnecessary parameters.
	 */
	public function _replyToGetField( $aField ) {
		
		$aOutput = array();
		$sValue = $aField['attributes']['value'];
		
		foreach( $aField['label'] as $sKey =>$sLabel ) {

			/* Prepare attributes */
			$aInputAttributes = array(
				'type'	=> 'radio',
				'checked'	=> $sValue == $sKey ? 'checked' : '',
				'value' => $sKey,
				'id' => $aField['input_id'] . '_' . $sKey,
				'data-default' => $aField['default'],
			) 
			+ $this->getFieldElementByKey( $aField['attributes'], $sKey, $aField['attributes'] )
			+ $aField['attributes'];
			$aLabelAttributes = array(
				'for'	=>	$aInputAttributes['id'],
				'class'	=>	$aInputAttributes['disabled'] ? 'disabled' : '',
			);

			/* Insert the output */
			$aOutput[] = 
				$this->getFieldElementByKey( $aField['before_label'], $sKey )
				. "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: {$aField['label_min_width']}px;'>"
					. "<label " . $this->generateAttributes( $aLabelAttributes ) . ">"
						. $this->getFieldElementByKey( $aField['before_input'], $sKey )
						. "<span class='admin-page-framework-input-container'>"
							. "<input " . $this->generateAttributes( $aInputAttributes ) . " />"	// this method is defined in the utility class	
						. "</span>"
						. "<span class='admin-page-framework-input-label-string'>"
							. $sLabel
						. "</span>"	
						. $this->getFieldElementByKey( $aField['after_input'], $sKey )
					. "</label>"
				. "</div>"
				. $this->getFieldElementByKey( $aField['after_label'], $sKey )
				;
				
		}
		$aOutput[] = $this->_getUpdateCheckedScript( $aField['_field_container_id'] );
		return implode( PHP_EOL, $aOutput );
			
	}
		/**
		 * Returns the JavaScript script that updates the checked attribute of radio buttons when the user select one.
		 * This helps repeatable field script that duplicate the last checked item.
		 * @sinec			3.0.0
		 */
		private function _getUpdateCheckedScript( $sFieldContainerID ) {
			return 
				"<script type='text/javascript' class='radio-button-checked-attribute-updater'>
					jQuery( document ).ready( function(){
						jQuery( '#{$sFieldContainerID} input[type=radio]' ).change( function() {
							jQuery( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio]' ).attr( 'checked', false );
							jQuery( this ).attr( 'checked', 'Checked' );
						});
					});				
				</script>";		
			
		}	
}
endif;