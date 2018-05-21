<?php

class toSendItCustomPost {

	protected static $lastPost      = null;
	protected        $lastWasHidden = true;

	private static $langDomain = 'default';

	public function setLangDomain($domain){
		self::$langDomain = $domain;
	}

	public static function set( $metadata, $valore, $idPost = null ) {
		global $post;
		if ( is_null( $idPost ) ) {
			$idPost = $post->ID;
		}

		update_post_meta( $idPost, $metadata, $valore );
	}


	static public function get( $metadata, $from = '', $to = '', $idPost = null ) {

		global $post;
		if ( is_null( $idPost ) ) {
			$idPost = $post->ID;
		}
		$value = get_post_meta( $idPost, $metadata, true );
		if ( $from != '' ) {
			$value = preg_replace( $from, $to, $value );
		}

		return $value;
	}

	public function register( $postName, $slug, $plurarArticle, $singularName, $pluralName, $supports, $metaBoxCallBack = null, $langDomain = '', $public = true, $capabilities = [] ) {

		$singularName = ucfirst( $singularName );

		$pluralName = ucfirst( $pluralName );

		$instrSingular = strtolower( $singularName );
		$instrPlural   = strtolower( $pluralName );
		$postSettings = [
			'labels'             => [
				'name'               => __( $pluralName, $langDomain ),
				'singular_name'      => __( $singularName, $langDomain ),
				'all_items'          => __( "All $plurarArticle$instrPlural", $langDomain ),
				'add_new_item'       => __( "New $instrSingular", $langDomain ),
				'add_new'            => __( "New $instrSingular", $langDomain ),
				'edit_item'          => __( "Edit $instrSingular", $langDomain ),
				'search_items'       => __( "Find $instrPlural", $langDomain ),
				'not_found'          => __( "$singularName not found", $langDomain ),
				'not_found_in_trash' => __( "No $instrPlural in trash", $langDomain ),
			],
			'public'             => true,
			'publicly_queryable' => $public,
			'has_archive'        => true,
			'rewrite'            => [ 'slug' => $slug ],
			'hierarchical'       => false,
			'supports'           => $supports,
			'register_meta_box_cb' => $metaBoxCallBack,
		];


		$postSettings = array_merge( $postSettings, $capabilities);

		register_post_type(
			$postName,
			$postSettings
		);

	}

	public function preparePostForMetabox( $sql ) {
		global $wpdb;
		$post = $wpdb->get_row( $sql );
		if ( is_null( $post ) ) {
			$post     = new stdClass();
			$post->ID = 0;
		} else {
			if ( ! isset( $post->ID ) && isset( $post->id ) ) {
				$post->ID = $post->id;
			}

		}
		self::$lastPost = $post;

	}

	public function drawField( $field, $meta, $displayInTableForm ) {

		if ( ! isset( $field[ 'std' ] ) ) {
			$field[ 'std' ] = '';
		}
		if ( ! isset( $field[ 'size' ] ) ) {
			$field[ 'size' ] = 30;
		}
		if ( ! isset( $field[ 'style' ] ) ) {
			$field[ 'style' ] = 'width:97%';
		}
		if ( ! isset( $field[ 'desc' ] ) ) {
			$field[ 'desc' ] = '';
		}
		if ( ! isset( $field[ 'display' ] ) ) {
			$field[ 'display' ] = '';
		}
		if ( ! isset( $field[ 'readonly' ] ) ) {
			$field[ 'readonly' ] = false;
		}
		$requiresLabel  = true;
		$fieldOutput    = '';
		$drawParagraphs = true;
		$readonly       = $field[ 'readonly' ] ? 'readonly="readonly"' : '';
		switch ( $field[ 'type' ] ) {
			case 'text':

				$fieldOutput = '<input type="text" ' . $readonly . ' name="' . $field[ 'id' ] . '" id="' . $field[ 'id' ] . '" value="' . ( $meta ? $meta : $field[ 'std' ] ) . '" size="' . $field[ 'id' ] . '" style="' . $field[ 'style' ] . '" />';
				break;
			case 'editor':
				$requiresLabel      = false;
				$displayInTableForm = false;
				echo( '<h4>' . $field[ 'name' ] . '</h4>' );
				wp_editor( ( $meta ? $meta : $field[ 'std' ] ), $field[ 'id' ] );
				$drawParagraphs = false;
				break;
			case 'textarea':
				$fieldOutput = '<textarea ' . $readonly . ' name="' . $field[ 'id' ] . '" id="' . $field[ 'id' ] . '" cols="60" rows="4" style="width:97%">' . htmlspecialchars( $meta ? $meta : $field[ 'std' ] ) . '</textarea>';
				break;
			case 'select':
				$fieldOutput = '<select name="' . $field[ 'id' ] . '" id="' . $field[ 'id' ] . '">';
				foreach ( $field[ 'options' ] as $option ) {
					$fieldOutput = '<option ' . ( $meta == $option ? ' selected="selected"' : '' ) . '>' . $option . '</option>';
				}
				$fieldOutput .= '</select>';
				break;
			case 'post_query':
				$isMultiple  = ( isset( $field[ 'multiple' ] ) && $field[ 'multiple' ] === true );
				$fieldOutput = '';
				if ( ! $isMultiple ) {
					$fieldOutput = '<select name="' . $field[ 'id' ] . '" id="' . $field[ 'id' ] . '">';

				} else {
					/*
					 * Trasformo il metakey in Array
					 */
					if ( ! is_array( $meta ) ) {
						$meta = [ $meta ];
					}
				}
				$posts = get_posts( $field[ 'sql' ] );
				if ( ! $isMultiple && isset( $field[ 'empty' ] ) ) {

					if ( isset( $field[ 'empty' ][ 0 ] ) ) {
						foreach ( $field[ 'empty' ] as $fld ) {
							$option      = $fld[ 'key' ];
							$text        = $fld[ 'value' ];
							$fieldOutput .= '<option ' . ( $meta == $option ? ' selected="selected"' : '' ) . ' value="' . $option . '">' . $text . '</option>';
						}
					} else {

						$option      = $field[ 'empty' ][ 'key' ];
						$text        = $field[ 'empty' ][ 'value' ];
						$fieldOutput .= '<option ' . ( $meta == $option ? ' selected="selected"' : '' ) . ' value="' . $option . '">' . $text . '</option>';

					}
				}
				foreach ( $posts as $loopIndex => $singlePost ) {
					$option = $singlePost->$field[ 'key' ];
					$text   = $singlePost->$field[ 'value' ];
					if ( $isMultiple ) {

						$isChecked = ( array_search( $option, $meta ) !== false ) ?
							'checked="checked"' : '';


						$optionId    = $field[ 'id' ] . '-' . $loopIndex;
						$fieldOutput .= '<input type="checkbox" ' . $isChecked . ' name="' . $field[ 'id' ] . '[]" value="' . $option . '" id="' . $optionId . '" /> ';
						$fieldOutput .= '<label for="' . $optionId . '">' . $text . '</label><br />';
					} else {
						$fieldOutput .= '<option ' . ( $meta == $option ? ' selected="selected"' : '' ) . ' value="' . $option . '">' . $text . '</option>';
					}
				}
				if ( ! $isMultiple ) {
					$fieldOutput .= '</select>';
				}
				break;
			case 'taxonomy':
				$tax = get_taxonomy( $field[ 'id' ] );
				if ( current_user_can( $tax->cap->assign_terms ) ) {

					$terms = get_terms( $field[ 'id' ], [ 'hide_empty' => false ] );
					if ( ! empty( $terms ) ) {
						if ( ! $displayInTableForm ) {
							$fieldOutput = "<strong>" . $field[ 'name' ] . ":</strong><br />";
						}
						$fieldId = $field[ 'id' ];
						global $post;
						foreach ( $terms as $term ) {
							$termSlug = esc_attr( $term->slug );
							$termName = $term->name;

							$fieldOutput .= "<input type=\"radio\" name=\"$fieldId\" id=\"$fieldId-$termSlug\"
							value =\"$termSlug\" " .
							                checked( true, is_object_in_term( $post->ID, $fieldId, $term ), false ) . " />";

							$fieldOutput .= "<label for=\"$fieldId-$termSlug\">$termName</label> <br />";
						}
					}
				}
				break;
			case 'radio':
				$fieldOutput = '';
				if ( $meta == '' && $field[ 'std' ] != '' ) {
					$meta = $field[ 'std' ];
				}
				if ( ! $displayInTableForm && ! empty( $field[ 'name' ] ) ) {
					$fieldOutput .= "<strong>" . $field[ 'name' ] . ":</strong><br />";
				}
				foreach ( $field[ 'options' ] as $index => $option ) {
					if ( $field[ 'display' ] == 'one-per-line' ) {
						$fieldOutput .= '<p>';
					}
					$fieldOutput .= "<span style=\"margin-right: 20px\"><input type=\"radio\" id=\"opt-{$field['id']}-$index\" name=\"{$field['id']}\" value=\"{$option['value']}\"" .
					                ( ( $meta == $option[ 'value' ] ) ? ' checked="checked"' : '' ) . " />";
					$fieldOutput .= "<label for=\"opt-{$field['id']}-$index\">{$option['name']}</label></span>";
					if ( $field[ 'display' ] == 'one-per-line' ) {
						$fieldOutput .= '</p>';
					}
				}
				$drawParagraphs = ( $field[ 'display' ] == 'one-per-line' );
				$requiresLabel  = false;
				break;
			case 'checkbox':

				if ( $field[ 'name' ] == '' ) {
					$requiresLabel = false;
				}

				if ( ! isset( $field[ 'options' ] ) ) {
					$fieldOutput = '<input type="checkbox" name="' . $field[ 'id' ] . '" id="' . $field[ 'id' ] . '"' . ( $meta ? ' checked="checked"' : '' ) . ' />';
					if($requiresLabel){
						$fieldOutput = '<label>' . $fieldOutput .  ' ' . $field['name'] . '</label>';
						$requiresLabel = false;
					}
				} else {
					if ( ! is_array( $meta ) ) {
						$meta = [ $meta ];
					}
					$fieldOutput = "<div>";

					foreach ( $field[ 'options' ] as $index => $option ) {
						$fieldOutput .= '<p>';
						$fieldOutput .= '<input type="checkbox" name="' . $field[ 'id' ] . '[]" ' .
						                'id="' . $field[ 'id' ] . '-' . $index . '" ' .
						                ( ( array_search( $option[ 'value' ], $meta ) !== false ) ? 'checked="checked" ' : '' ) .
						                'value="' . $option[ 'value' ] . '" />';
						$fieldOutput .= '<label for="' . $field[ 'id' ] . '-' . $index . '">' . $option[ 'name' ] . '</label>';
						$fieldOutput .= '</p>';
					}
					$fieldOutput .= "</div>";
				}
				break;
			case 'datetime':
				$requiresLabel  = false;
				$fieldOutput    = $this->createTimeFieldFor( $field[ 'id' ], $field[ 'name' ], false );
				$drawParagraphs = false;
				break;
			case 'date':

				$requiresLabel  = false;
				$fieldOutput    = $this->createTimeFieldFor( $field[ 'id' ], $field[ 'name' ], false, true );
				$drawParagraphs = false;
				break;
			case 'hidden':
				$requiresLabel  = false;
				$fieldOutput    = '<input type="hidden" name="' . $field[ 'id' ] . '" value="' . ( $meta ? $meta : $field[ 'std' ] ) . '" />';
				$drawParagraphs = false;
				break;
			case 'submit':
				$requiresLabel = false;
				if ( ! isset( $field[ 'id' ] ) ) {
					$field[ 'id' ] = '';
				}
				$fieldOutput = get_submit_button( $field[ 'name' ], 'primary', $field[ 'id' ], false );
				break;
		}

		if ( !empty($field[ 'desc' ]) ) {
			$fieldOutput .= '<p class="help">' . $field[ 'desc' ] . '</p>';
		}

		if ( $displayInTableForm ) {
			if ( $field[ 'type' ] == 'hidden' ) {
				if ( ! $this->lastWasHidden ) {
					echo '</table>';
				}
				$this->lastWasHidden = true;
				echo $fieldOutput;
			} else {

				if ( $this->lastWasHidden ) {
					echo '<table class="form-table">';
					$this->lastWasHidden = false;
				}

				echo
				'<tr>',
				'<th style="width:20%">',
				$requiresLabel ? '<label for="' . $field[ 'id' ] . '">' : '',
				$requiresLabel ? $field[ 'name' ] : '',
				$requiresLabel ? '</label>' : '',
				'</th>',
				'<td>',
				'</td><td>',
				$fieldOutput,
				'</td></tr>';
			}

		} else {
			if ( $requiresLabel ) {
				echo( '<label for="' . $field[ 'id' ] . '"><strong>' . $field[ 'name' ] . '</strong>:</label>' );
			}
			echo
			$drawParagraphs ? '<p>' : '',
			$fieldOutput,
			$drawParagraphs ? '</p>' : '';

		}

	}

	public function buildFormMetaBox( $post, $fields, $createNonce = false, $displayInTableForm = true ) {
		// Use nonce for verification
		$this->lastWasHidden = true;
		if ( is_null( $post ) ) {
			$post = self::$lastPost;
		}
		if ( is_string( $post ) ) {
			$this->preparePostForMetabox( $post );
			$post = self::$lastPost;
		}
		self::$lastPost = $post;
		$theClass       = ( get_class( $this ) );
		if ( $createNonce ) {

			wp_nonce_field( $theClass . $post->ID, $theClass, false );
		}
		$post = (array) $post;
		foreach ( $fields as $field ) {
			// get current post meta data
			if ( isset( $field[ 'id' ] ) ) {
				if ( isset( $post[ $field[ 'id' ] ] ) ) {
					$meta = $post[ $field[ 'id' ] ];
				} else {
					$meta = self::get( $field[ 'id' ], '', '', $post[ 'ID' ] );


				}
			} else {
				$meta = '';
			}
			$this->drawField( $field, $meta, $displayInTableForm );
		}
		if ( $displayInTableForm ) {
			echo '</table>';
		}
	}


	protected function createTimeFieldFor( $id, $label, $echo = true, $noTime = false ) {
		global $wp_locale, $post;

		if ( ! is_null( self::$lastPost ) ) {
			$thePost = self::$lastPost;
		} else {
			$thePost = $post;
		}
		$datef = __( $noTime ? 'M j, Y ' : 'M j, Y @ G:i', self::$langDomain );
		$stamp = $label . ' <strong>%1$s</strong>';
		if ( is_null( $thePost ) ) {
			$post_date = date( 'Y-m-d H:i:s' );
		} else {
			$post_date = $thePost->$id;
		}

		if ( ! isset( $post_date ) ) {
			$post_date = get_post_meta( $post->ID, $id, true );
		}
		if ( $post_date == '' ) {
			$post_date = date( 'Y-m-d H:i:s' );
		}
		$jj = mysql2date( 'd', $post_date, false );
		$mm = mysql2date( 'm', $post_date, false );
		$aa = mysql2date( 'Y', $post_date, false );
		$hh = mysql2date( 'H', $post_date, false );
		$mn = mysql2date( 'i', $post_date, false );
		$ss = mysql2date( 's', $post_date, false );

		$cur_jj = $jj;
		$cur_mm = $mm;
		$cur_aa = $aa;
		$cur_hh = $hh;
		$cur_mn = $mn;
		$cur_ss = $ss;

		$month = "<select id=\"mm-$id\" name=\"mm_$id\">\n";
		for ( $i = 1; $i < 13; $i = $i + 1 ) {
			$monthnum = zeroise( $i, 2 );
			$month    .= "\t\t\t" . '<option value="' . $monthnum . '"';
			if ( $i == $mm ) {
				$month .= ' selected="selected"';
			}
			/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
			$month .= '>' . sprintf( __( '%1$s-%2$s', self::$langDomain ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
		}
		$month .= '</select>';

		$day  = "<input type=\"text\" id=\"jj-$id\" class=\"field_day\" name=\"jj_$id\" value=\"$jj\" size=\"2\" maxlength=\"2\" autocomplete=\"off\" />";
		$year = "<input type=\"text\" id=\"aa-$id\" class=\"field_year\" name=\"aa_$id\" value=\"$aa\" size=\"2\" maxlength=\"4\" autocomplete=\"off\" />";
		if ( $noTime ) {
			$hour   = "<input type=\"hidden\" id=\"hh-$id\" name=\"hh_$id\" value=\"$hh\" />";
			$minute = "<input type=\"hidden\" id=\"mn-$id\" name=\"mn_$id\" value=\"$mn\" />";

		} else {
			$hour   = "<input type=\"text\" id=\"hh-$id\" class=\"field_hour\" name=\"hh_$id\" value=\"$hh\" size=\"2\" maxlength=\"2\" autocomplete=\"off\" />";
			$minute = "<input type=\"text\" id=\"mn-$id\" class=\"field_minute\" name=\"mn_$id\" value=\"$mn\" size=\"2\" maxlength=\"2\" autocomplete=\"off\" />";
		}
		$date = date_i18n( $datef, strtotime( $post_date ) );


		$hiddenOutput = "";

		foreach ( [ 'mm', 'jj', 'aa', 'hh', 'mn' ] as $timeunit ) {
			$hiddenOutput .= '<input type="hidden" id="hidden_' . $timeunit . '_' . $id . '" name="hidden_' . $timeunit . '_' . $id . '" value="' . $$timeunit . '" />' . "\n";
			$cur_timeunit = 'cur_' . $timeunit;
			$hiddenOutput .= '<input type="hidden" id="' . $cur_timeunit . '_' . $id . '" name="' . $cur_timeunit . '_' . $id . '" value="' . $$cur_timeunit . '" />' . "\n";
		}

		$_noTime = $noTime ? 'date_only' : '';
		$output  = <<<EOT
			<div class="misc-pub-section curtime forma_time" >
				<span id="timestamp_%1\$s" class="$_noTime">%2\$s</span>
				<a href="#timestamp_%1\$s" class="edit-timestamp hide-if-no-js">%5\$s</a>
				<div id="timestamp_%1\$sdiv" class="hide-if-js">
					<div class="timestamp-wrap">
						%3\$s
					</div><input type="hidden" id="ss_%1\$s" name="ss" value="$ss" />
					%4\$s
					<p>
						<a href="#timestamp_%1\$s" class="save-timestamp hide-if-no-js button">%6\$s</a>
						<a href="#timestamp_%1\$s" class="cancel-timestamp hide-if-no-js">%7\$s</a>
					</p>
				</div>
			</div>
EOT;
		$output  = sprintf( $output,
		                    $id,
		                    sprintf( $stamp, $date ),
			/* translators: 1: month input, 2: day input, 3: year input, 4: hour input, 5: minute input */
			                sprintf( __( ( $noTime ? '%1$s%2$s, %3$s%4$s%5$s' : '%1$s%2$s, %3$s @ %4$s : %5$s' ), self::$langDomain ), $month, $day, $year, $hour, $minute ),
			                $hiddenOutput,
			                __( 'Edit', self::$langDomain ),
			                __( 'OK', self::$langDomain ),
			                __( 'Cancel', self::$langDomain )
		);
		if ( $echo ) {
			echo $output;
		}

		return $output;

	}

	protected function mergePostDate( $fieldSuffix ) {
		$ret = $_POST[ 'aa_' . $fieldSuffix ] . '-' . $_POST[ 'mm_' . $fieldSuffix ] . '-' . $_POST[ 'jj_' . $fieldSuffix ] .
		       ( ( isset( $_POST[ 'hh_' . $fieldSuffix ] ) && isset( $_POST[ 'mn' . $fieldSuffix ] ) ) ? ( ' ' . $_POST[ 'hh_' . $fieldSuffix ] . ':' . $_POST[ 'mn_' . $fieldSuffix ] ) : '' );

		return $ret;

	}
}
