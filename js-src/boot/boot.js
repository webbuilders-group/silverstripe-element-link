/* global jQuery */
import Injector from 'lib/Injector';
import ElementDropdownField from '../components/ElementDropdownField';

document.addEventListener('DOMContentLoaded', () => {
    Injector.component.register('ElementDropdownField', ElementDropdownField);

    Injector.transform(
        'element-link',
        (updater) => {
            updater.form.alterSchema(
                'Link.EditingLinkInfo',
                (form) =>
                    form
                        .updateField('PageID', {
                            onChange: function(value) {
                                // Workaround to get ElementDropdownField working
                                jQuery('input[name=PageID]').val(value).trigger('change', value);
                            },
                        })
                        .getState()
            );
        }
    );
});
