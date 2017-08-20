jQuery( document ).ready(function( $ ) {

    Vue.use( VueLocalStorage );
    
    var vm = new Vue({
		el: '#mk-theme-options',
            
        components: {
	        "vue-form-generator": VueFormGenerator.component
	    },
        
        data: {
            activeMenu: 'general',
            activeSubmenu: '',
            activeTab: 'site_settings',
            menu: mk_data.menu,
            model: mk_data.values || {},
            schema: mk_data.schema,
            helpStatus: false,
            MkModelSync: mk_data.values || {},
            pageInit: false,
        },

        watch: {
            MkModelSync: function( val, oldVal ){
                var tabUpdated = window.location.hash.substr(1) ? window.location.hash.substr(1) : this.activeTab;
                this.$ls.setInit( 'MkModelSync', val );
            },
            helpStatus: function( val, oldVal ) {
                if ( val ) {
                    this.setHelpText( this );
                }
            }
        },

        created: function() {
            var vm = this;
            vm.setDefaultOptions( vm, mk_data );
        },

        mounted: function() {
            var vm = this;
            var submenuKey = window.location.hash.substr(1) ? window.location.hash.substr(1) : vm.activeTab;

            vm.setActiveTab( submenuKey );
            vm.setDataVisible( mk_data );
            vm.setHelpText( vm );
            vm.restoreHistory();
            vm.restoreDefaults();
            vm.importThemeOptions();
            vm.stickyHeader();
            vm.saveBtnAnimation();
            vm.syncThemeOptionsLocal();
        },   
        
        updated: function() {
            var vm = this;
            
            vm.setHelpText( vm );
        },   
        
        methods: {
            
            stickyHeader: function () {
                var $header = $('.mka-to-header'),
                    headerTopOffset = $header.offset().top - 30;  // Negetive 30 to account for WP Admin Bar

                $(window).scroll(function(){
                    if ( $(window).scrollTop() > headerTopOffset && !$header.hasClass('mka-to-header--sticky') ) {
                        $header.addClass('mka-to-header--sticky');
                    } else if ( $(window).scrollTop() < headerTopOffset ) {
                        $header.removeClass('mka-to-header--sticky');
                    }
                });
            },

            saveBtnAnimation: function() {
                $('.mka-to-save-button').on('mk_show_progress', function() {
                    TweenLite.to( $(this).find('.mka-button-icon-spinner'), 0, { css: { scale: 1, display: 'inline-block'}, ease: Power1.easeOut, delay: 0 });
                });

                $('.mka-to-save-button').on('mk_hide_progress', function() {
                    TweenLite.to( $(this).find('.mka-button-icon-spinner'), 0.1, { css: { scale: 0.1, display: 'none'}, ease: Power1.easeOut, delay: 0 });
                });

                $('.mka-to-save-button').on('mk_show_success', function() {
                    TweenLite.to( $(this).find('.mka-button-icon-success'), 0, { css: { 'clip-path': 'circle(43% at 5% 108%)'}, ease: Power1.easeOut, delay: 0 });
                    TweenLite.to( $(this).find('.mka-button-icon-success'), 0.6, { css: { 'clip-path': 'circle(130% at 9% -9%)', display: 'inline-block'}, ease: Power1.easeOut, delay: 0.1 });
                });

                $('.mka-to-save-button').on('mk_hide_success', function() {
                    TweenLite.to( $(this).find('.mka-button-icon-success'), 0, { css: { display: 'none'}, ease: Power1.easeOut, delay: 0 });
                });
            },

            setActiveTab: function ( submenuKey ) {  
                var menuKey = $( 'a[href="#' + submenuKey + '"]' ).parents( '.mka-to-nav-item' ).data( 'id' );  
                
				if ( _.size(this.menu[ menuKey ].submenu ) <= 1 || menuKey === submenuKey ) {
					submenuKey = this.menu[ menuKey ]['default'];
				}

                this.activeMenu = menuKey;
                this.activeSubmenu = submenuKey;
                this.activeTab = submenuKey;
            },
            
            setDefaultOptions: function( vm, mk_data ) {
                if ( _.isEmpty( mk_data.values ) ) {
                    vm.model = {};
                    
                    $.each( mk_data.schema, function( key, tab ) {
                        $.each( tab.sections, function( key, section ) {
                            var obj = VueFormGenerator.schema.createDefaultObject( section );
                            
                            $.each( obj, function( key, value ) {
                                vm.$set(vm.model, key, value);
                            } );
                        } );
                    } );
                }
            },
            
            setDataVisible: function( data ) {
                var vm = this;
                
                $.each( data.schema, function( key, tab ) {
                    $.each( tab.sections, function( key, section ) {
                        vm.setVisible( section );
                        
                        $.each( section.fields, function( key, field ) {
                            vm.setVisible( field );
                        });
                    });
                });
            },
            
            setVisible: function( item ) {
                if ( item.hasOwnProperty( 'condition' ) ) {
                    var vm = this;
                    $.extend( item ,{
                        visible: function( model ) {
                            if ( item.hasOwnProperty( 'help' ) ) {
                                vm.helpStatus = true;
                            }

                            if ( item.condition.hasOwnProperty( 'model' ) && item.condition.hasOwnProperty( 'value' ) ) {
                                return model && model[item.condition.model] == item.condition.value;
                            }

                            if ( typeof item.condition === 'object' && model ) {
                                var status   = true;
                                    relation = ( item.condition.hasOwnProperty( 'relation' ) ) ? item.condition.relation : 'AND';

                                $.each( item.condition, function( key, field ) {
                                    if ( typeof field !== 'object' ) {
                                        return true; // Continue $.each iteration, not return visible true;
                                    }

                                    if ( model[field.model] != field.value ) {
                                        status = false;
                                    } else {
                                        if ( relation == 'OR' ) {
                                            status = true;
                                            return false; // Break $.each iteration, not return visible value.
                                        }
                                    }
                                });

                                return status;
                            }

                            return false;
                        }
                    });
                }
            },
            
            sectionVisible: function( section ) {
                if ( _.isFunction( section.visible ) ) {
                    return section.visible.call(this, this.model, section, this);
                }
					
				if ( ! section.hasOwnProperty( 'visible' ) ) {
					return true;
                }
                
				return section.visible;
                
                // This is also another soluion.
                // if ( ! section.hasOwnProperty( 'condition' ) ) {
                //     return true;
                // }
                // 
                // if ( this.model[section.condition.model] == section.condition.value ) {
                //     return true;
                // }
				// return false;
			},
            
            getClasses: function( item ) {
                var baseClasses = {};
                
				if ( _.isArray( item.styleClasses ) ) {
					each( item.styleClasses, function( c ) {
                        baseClasses[c] = true
                    } );
				}
				else if ( _.isString( item.styleClasses ) ) {
					baseClasses[item.styleClasses] = true;
				}
                
				return baseClasses;
			},
            
            setHelpText: function( vm ) {
                // @todo refactor this and find another solution. It's heavy.
                $( vm.$el ).find('.help').each( function() {
                    var helpText = $(this).find('.helpText').html();
                    var helpHtml =
                    '<div class="mka-wrap mka-tip-wrap">\
                        <a href="#" class="mka-tip">\
                            <span class="mka-tip-icon">\
                                <span class="mka-tip-arrow"></span>\
                            </span>\
                            <span class="mka-tip-ripple"></span>\
                        </a>\
                        <div class="mka-tip-content">' + helpText + '</div>\
                    </div>';
                    $(this).replaceWith(helpHtml);
                });

                if ( vm.helpStatus ) {
                    vm.helpStatus = false;
                }
            },

            restoreHistory: function( vm ) {
                $('.mka-to-sidebar-history').on('click', function(e) {
                    e.preventDefault();
                    jQuery.post(ajaxurl, {
                        action: 'mk_list_theme_option_revision',
                    }).done(function(response) {
                        if (response.status === true) {

                            var customHtml = '';
                            customHtml += '<div>';
                                customHtml += '<h3 class="mka-modal-title">Choose a Step Backward</h3>';
                                customHtml += '<p class="mka-modal-desc">Notice: This action is not reversable</p>';
                                customHtml += '<div class="mka-modal-desc">';
                                    customHtml += '<ul class="mka-modal-step-list">';
                                    jQuery.each(response.data, function(key, val) {
                                        customHtml += '<li><a href="#" data-name="'+val+'" class="mk_revision_item">Revision Number : <strong>'+ (key+1) +'</strong> @ <strong>'+val+'</strong></a></li>'
                                    });
                                customHtml += '</ul>';
                                customHtml += '</div>';
                            customHtml += '</div>';

                            var modal = mk_modal({
                                html: $(customHtml),
                                type: '',
                                showCancelButton: true,
                                showConfirmButton: true,
                                showCloseButton: true,
                                showLearnmoreButton: false,
                                showProgress: false,
                                confirmButtonText: 'OK',
                                cancelButtonText: 'Cancel',
                                closeOnConfirm: false,
                                onConfirm: function() {

                                },
                                onCancel: function() {

                                },
                                onClose: function() {

                                }
                            });
                        }
                    }).fail(function(data) {
                        console.log('Failed msg : ', data);
                    });
                });

                jQuery(document).on('click', '.mk_revision_item', function(e) {

                    e.preventDefault();
                    jQuery.post(ajaxurl, {
                        action: 'mk_restore_theme_option_revision',
                        revision_name: jQuery(this).data('name'),
                    }).done(function(response) { 
                        if (response.status == true) {
                            window.location.reload();
                        }
                    }).fail(function(data) {
                        console.log('Failed msg : ', data);
                    });
                });

            },

            restoreDefaults: function() {

                var model = this.model;
                
                $('.mka-to-sidebar-restore').on('click', function(e) {
                    e.preventDefault();

                    var modal = mk_modal({
                        type: 'warning',
                        showCancelButton: true,
                        showConfirmButton: true,
                        showCloseButton: false,
                        showLearnmoreButton: false,
                        showProgress: false,
                        confirmButtonText: 'OK',
                        cancelButtonText: 'Cancel',
                        closeOnConfirm: false,
                        title: 'Are you sure you want to restore to defaults state?',
                        onConfirm: function() {

                            mk_modal({
                                type: 'warning',
                                showCancelButton: true,
                                showConfirmButton: true,
                                showCloseButton: false,
                                showLearnmoreButton: false,
                                showProgress: true,
                                indefiniteProgress: true,
                                showCancelButton: false,
                                showConfirmButton: false,
                                closeOnConfirm: false,
                                title: 'Restoring options to defautls state...',
                            });

                            model.security = $('.mka-to-save-button').data('nonce');
                            model.button_clicked = 'reset_theme_options';
                            model.action = 'mk_theme_save';

                            jQuery.post(ajaxurl, model).done(function(response) {
                                if (response.data.element === 'mk-success-reset') {
                                    mk_modal({
                                        type: 'success',
                                        showCancelButton: false,
                                        showConfirmButton: true,
                                        showCloseButton: false,
                                        showLearnmoreButton: false,
                                        showProgress: false,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        closeOnConfirm: false,
                                        confirmButtonText: 'OK',
                                        title: 'All options are restored to defaults state. The page will reload in a few seconds.',
                                    });
                                    window.location.reload();
                                }
                            }).fail(function(data) {
                                console.log('Restore Failed msg : ', data);
                            });
                        }
                    });

                });

            },
            
            prettyJSON: function(json) {
                if (json) {
                    json = JSON.stringify(json, undefined, 4);
                    json = json.replace(/&/g, '&').replace(/</g, '<').replace(/>/g, '>');
                    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
                        var cls = 'number';
                        if (/^"/.test(match)) {
                            if (/:$/.test(match)) {
                                cls = 'key';
                            } else {
                                cls = 'string';
                            }
                        } else if (/true|false/.test(match)) {
                            cls = 'boolean';
                        } else if (/null/.test(match)) {
                            cls = 'null';
                        }
                        return '<span class="' + cls + '">' + match + '</span>';
                    });
                }
            },

            /**
             * Check if the keys or values has been checked or changed. Then store it inside a temporary variable.
             * @param  {mixed}   a          The main parameter need to check.
             * @param  {mixed}   b          The old parameter as comparison.
             * @param  {mixed}   r          Temporary variable to store all changed values.
             * @param  {boolean} reversible Is reversible (true) or not (false - default)?
             * @return {mixed}              Return the changed keys and values.
             */
            deepDiff: function ( a, b, r, reversible ) {
                var vm = this;
                _.each( a, function( v, k ) {
                    if ( _.isObject( r ) ) {
                        if ( r.hasOwnProperty( k ) ) {
                            return;
                        }

                        var diff = false;
                        if ( b.hasOwnProperty( k ) ) {
                            diff = ( _.isArray( v ) ) ? JSON.stringify( b[k] ) === JSON.stringify(v) : b[k] === v;
                        }

                        if ( diff ) {
                            return;
                        }
                    }

                    r[k] = ( _.isObject(v) && ! _.isArray(v) ) ? vm.objectDiff( v, b[k], reversible ) : v;
                });
            },

            /**
             * Recursion function to call deepDiff and check if the main parameter is object or array.
             * @param  {mixed}   a          The main parameter need to check.
             * @param  {mixed}   b          The old parameter as comparison.
             * @param  {boolean} reversible Is reversible (true) or not (false - default)?
             * @return {mixed}              Return the changed keys and values.
             */
            objectDiff: function ( a, b, reversible ) {
                var r = ( _.isObject(a) ) ? {} : [];
                this.deepDiff( a, b, r, reversible );
                if ( reversible )
                    this.deepDiff( b, a, r, reversible );
                return r;
            },

            /**
             * Get the updated values of the sync options.
             * @param  {object} val    The changed or new value of model.
             * @param  {object} oldVal The last value of model.
             * @param  {objecy} model  Current model value in the tab.
             * @return {object}        The synced values.
             */
            updateDiff: function ( val, oldVal, model ) {
                if ( _.isEmpty( oldVal ) || _.isEmpty( model ) ) {
                    return val;
                }

                var diff  = vm.objectDiff( val, oldVal );

                if ( _.isObject( diff ) && ! _.isEmpty( diff ) ) {
                    _.each( diff, function( val, key ) {
                        model[ key ] = val;
                    });
                }

                return model;
            },

            importThemeOptions: function() {
                var dataModel = this.model;
                jQuery(document).on('click', '#mka_to_import_theme_options', function(e) {
                    
                    // Notifies user about the importing action.
                    $( this ).val( 'Importing ...' );
                    
                    var security = jQuery(this).attr("data-nonce");
                    dataModel.security = security;
                    dataModel.button_clicked = 'import_theme_options';
                    wp.ajax.send( "mk_theme_save", {
                        success: reloadPage(),
                        data: dataModel
                    });
                    // Will remove when animation is implemented
                    function reloadPage() {
                        setTimeout(function(){
                            location.href = window.location.href.split('#')[0];
                        }, 3000);
                    }
                });
            },

            /**
             * Sync the theme options use vue-ls and localstorage. Then, then change the tab active
             * and log the sync information.
             */
            syncThemeOptionsLocal: function() {
                var vm = this;
                this.MkModelSync = this.$ls.get( 'MkModelSync', this.model );
                this.$ls.on( 'MkModelSync', function( val, oldVal, uri ) {
                    var MkPageSave  = vm.$ls.get( 'MkPageSave', false );
                    if ( vm.pageInit && val && MkPageSave ) {
                        vm.model = vm.updateDiff( val, oldVal, vm.model );
                        vm.syncThemeOptionsTab( val );
                        vm.syncThemeOptionsLog( vm );
                    }
                });
                this.syncThemeOptionsLoad();
            },

            /**
             * Set some important information when user open and close the TO page. Also clear all the
             * localstorage data after all the TO page closed.
             */
            syncThemeOptionsLoad: function() {
                var vm = this;
                window.onload = function ( event ) {
                    var MkPageOpen = vm.$ls.get( 'MkPageOpen', 0 );
                    vm.$ls.setInit( 'MkPageOpen', MkPageOpen + 1 );
                    vm.$ls.setInit( 'MkPageSave', false );
                    vm.$ls.setInit( 'MkPageSynced', 0 );
                    vm.pageInit  = true;
                };
                window.onbeforeunload = function ( event ) {
                    var MkPageOpen = vm.$ls.get( 'MkPageOpen', 1 );
                    if ( MkPageOpen > 1 ) {
                        vm.$ls.setInit( 'MkPageOpen', MkPageOpen - 1 );
                    } else {
                        vm.$ls.remove( 'MkModelSync' );
                        vm.$ls.remove( 'MkPageSave' );
                        vm.$ls.remove( 'MkPageOpen' );
                        vm.$ls.remove( 'MkPageSynced' );
                    }
                };
            },

            /**
             * Set the Theme Options tab to display the synced data.
             * @param  {object} MkModelSync New synced data.
             */
            syncThemeOptionsTab: function( MkModelSync ) {
                var vm = this;
                var tabDef = 'site_settings';
                var tabKey = window.location.hash.substr(1) ? window.location.hash.substr(1) : this.activeTab;

                if ( tabKey == 'site_settings' || tabKey == 'general' ) {
                    tabDef = 'logo_title';
                }

                var MkPageSave = this.$ls.get( 'MkPageSave', false );

                if ( MkModelSync && MkPageSave ) {
                    vm.setActiveTab( tabDef );
                    setTimeout( function() { vm.setActiveTab( tabKey ); }, 1000);
                }
            },

            /**
             * Log some informations after sync process done.
             * @param  {object} vm The instance object of Vue.
             */
            syncThemeOptionsLog: function( vm ) {
                var MkPageSynced = vm.$ls.get( 'MkPageSynced', 1 );
                if ( MkPageSynced > 1 ) {
                    vm.$ls.setInit( 'MkPageSynced', MkPageSynced - 1 );
                } else {
                    vm.$ls.setInit( 'MkPageSave', false );
                    vm.$ls.setInit( 'MkPageSynced', 0 );
                }
            },
            
            save: function(event) {
                // @todo the whole method needs to be refactored when previous
                // theme options is dropped. 
                var vm = this;
                event.preventDefault();

                /* Sync Section */
                this.MkModelSync = {};
                this.MkModelSync = this.model;
                this.$ls.setInit( 'MkPageSave', true );
                var MkPageOpen   = vm.$ls.get( 'MkPageOpen', 0 );
                this.$ls.setInit( 'MkPageSynced', MkPageOpen - 1 );
                /* End of Sync Section */

                var send_data = $.extend( {}, this.model );; 
                $('.mka-to-save-button').trigger('mk_show_progress');
                
                send_data.action = 'mk_theme_save';
                send_data.security = $('.mka-to-save-button').data('nonce');
                send_data.button_clicked = 'save_theme_options_top';

                // Delete export options data from Model
                delete send_data.theme_export_options;
                
                jQuery.post( ajaxurl, send_data )
                    .done(function( response ) { 
                        if ( response.status == true ) {
                            vm.saveSuccess( response ); 
                            return;
                        }
                        vm.saveError( response );
                    })
                    .fail(function( response ) {
                        console.log('Server response (error) : ' , response );
                    });

                // Disabled for the sake of messaging. 
                // wp.ajax.send( "mk_theme_save", {
                //     success:  this.saveSuccess,
                //     error: this.saveError,
                //     data: send_data
                // });
            },
            
            saveSuccess: function( response ) {
                if ( response.data.hasOwnProperty( 'theme_export_options' ) ){
                    this.model.theme_export_options = response.data.theme_export_options;
                }
                
                $( '.mka-to-save-button' ).trigger( 'mk_hide_progress' );
                $( '.mka-to-save-button' ).trigger( 'mk_show_success' );
                $('.mka-save-response').removeClass( 'mka-has-error' ).text( response.message );
                
                setTimeout( function() {
                    $( '.mka-to-save-button' ).trigger( 'mk_hide_success' );
                    $( '.mka-save-response' ).text('');
                }, 3000);
            },
            saveError: function( response ) {
                $( '.mka-to-save-button' ).trigger( 'mk_hide_progress' );
                $( '.mka-save-response' ).addClass( 'mka-has-error' ).text( response.message );
                
                setTimeout( function() {
                    $( '.mka-save-response' ).text('');
                }, 3000);
            },
		}
    }); // vm
        
});