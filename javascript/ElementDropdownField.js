(function($) {
    $.entwine('dependentdropdown', function ($) {
        $('.depended-on-field .treedropdown[data-dependent-field] .treedropdown').entwine({
            Loading: false,

            onadd: function() {
                const self = $(this).closest('.treedropdown[data-dependent-field]');
                const dependentField = self.attr('data-dependent-field');
                if (dependentField) {
                    const drop = $('select[name=' + $.escapeSelector(dependentField) + '], select[name=' + $.escapeSelector(dependentField + '[]') + ']');
                    const depends = self.find('input[name]');

                    if (depends) {
                        depends.on('change', function () {
                            if (!this.value || this.value <= 0) {
                                drop.disable(drop.attr('data-empty') || '');
                            } else {
                                drop.disable('Loading...');

                                $.get(drop.data('link'), {
                                    val: this.value,
                                },
                                function (data) {
                                    drop.enable();

                                    if (drop.attr('data-empty')) {
                                        drop.append($('<option />').val('').text(drop.attr('data-empty')));
                                    }

                                    if (data.length > 0) {
                                        $.each(data, function () {
                                            drop.append($('<option />').val(this.k).text(this.v));
                                        });
                                    } else {
                                        drop.disable(drop.attr('data-empty') || '');
                                    }

                                    drop.trigger('liszt:updated').trigger('chosen:updated').trigger('change');
                                });
                            }
                        });

                        if (!depends.val() || depends.val() <= 0) {
                            drop.disable(drop.attr('data-empty') || '');
                        }
                    }
                }
            },
        });
    });
})(jQuery);
