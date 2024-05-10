import React, { Component } from 'react';
import { findDOMNode } from 'react-dom';
import fieldHolder from 'components/FieldHolder/FieldHolder';
import i18n from 'i18n';
import { Input } from 'reactstrap';
import PropTypes from 'prop-types';

class ElementDropdownField extends Component {
    constructor(props) {
        super(props);

        this.handleChange = this.handleChange.bind(this);
    }

    /**
     * Builds the select field in readonly mode with current props
     *
     * @returns {object}
     */
    getReadonlyField() {
        let label = this.props.source && this.props.source.find((item) => item.value === this.props.value);

        label = (typeof label === 'string' ? label : (this.props.value || ''));

        return <Input plaintext {...this.getInputProps()} tag="p">{label}</Input>;
    }

    /**
     * Builds the select field with current props
     *
     * @returns {object}
     */
    getSelectField() {
        // .slice() to copy the array, because we could modify it with an empty item
        const options = (this.props.source ? this.props.source.slice() : []);

        if (this.props.data.hasEmptyDefault && !options.find((item) => !item.value)) {
            options.unshift({
                value: '',
                title: this.props.data.emptyString,
                disabled: false,
            });
        }

        return (
            <Input type="select" {...this.getInputProps()}>
                { options.map((item, index) => {
                    const key = `${this.props.name}-${item.value || `empty${index}`}`;
                    const description = item.description || null;

                    return (
                        <option key={key} value={item.value} disabled={item.disabled} title={description}>
                        {item.title}
                        </option>
                    );
                }) }
            </Input>
        );
    }

    /**
     * Fetches the properties for the select field
     *
     * @returns {object} properties
     */
    getInputProps() {
        const props = {
            className: `${this.props.className} ${this.props.extraClass} no-chosen`,
            id: this.props.id,
            name: this.props.name,
            disabled: this.props.disabled,
            'data-link': this.props.link,
            'data-depends': this.props.depends,
            'data-unselected': this.props.unselected,
            'data-empty': this.props.data.emptyString,
        };

        if (!this.props.readOnly) {
            Object.assign(props, {
                onChange: this.handleChange,
                value: this.props.value || '',
            });
        }

        return props;
    }

    /**
     * Handles changes to the select field's value.
     *
     * @param {Event} event
     */
    handleChange(event) {
        if (typeof this.props.onChange === 'function') {
            this.props.onChange(event, { id: this.props.id, value: event.target.value });
        }
    }

    render() {
        let field = null;
        if (this.props.readOnly) {
            field = this.getReadonlyField();
        } else {
            field = this.getSelectField();
        }

        return field;
    }

    componentDidMount() {
        const form = findDOMNode(this).closest('form');
        const input = form.querySelector('input[name=' + CSS.escape(this.props.depends) + ']');
        if (input) {
            input.closest('.treedropdownfield').setAttribute('data-dependent-field', this.props.name);
        }
    }
}

ElementDropdownField.propTypes = {
    id: PropTypes.string,
    name: PropTypes.string.isRequired,
    onChange: PropTypes.func,
    value: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    readOnly: PropTypes.bool,
    disabled: PropTypes.bool,
    link: PropTypes.string.isRequired,
    depends: PropTypes.string.isRequired,
    unselected: PropTypes.string,
    source: PropTypes.arrayOf(PropTypes.shape({
        value: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
        title: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
        description: PropTypes.string,
        disabled: PropTypes.bool,
    })),
    data: PropTypes.oneOfType([
        PropTypes.array,
        PropTypes.shape({
            hasEmptyDefault: PropTypes.bool,
            emptyString: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
        }),
    ]),
};

ElementDropdownField.defaultProps = {
    source: [],
    extraClass: '',
    className: '',
    data: {
        emptyString: i18n._t('Boolean.ANY', 'Any'),
    },
};

export { ElementDropdownField as Component };

export default fieldHolder(ElementDropdownField);
