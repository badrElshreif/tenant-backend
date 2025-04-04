import React from 'react';

/**
 * Switch component for toggling boolean values
 * 
 * @param {Object} props - Component props
 * @param {boolean} props.checked - Whether the switch is checked
 * @param {Function} props.onChange - Function to call when switch is toggled
 * @param {boolean} props.disabled - Whether the switch is disabled
 * @param {string} props.size - Size of the switch (sm, md, lg)
 * @returns {JSX.Element} - Rendered component
 */
export default function Switch({ checked = false, onChange, disabled = false, size = 'md' }) {
    // Size classes
    const sizes = {
        sm: {
            switch: 'w-8 h-4',
            dot: 'h-3 w-3',
            translate: 'translate-x-4'
        },
        md: {
            switch: 'w-11 h-6',
            dot: 'h-5 w-5',
            translate: 'translate-x-5'
        },
        lg: {
            switch: 'w-14 h-7',
            dot: 'h-6 w-6',
            translate: 'translate-x-7'
        }
    };

    const currentSize = sizes[size] || sizes.md;

    return (
        <button
            type="button"
            className={`${checked ? 'bg-indigo-600' : 'bg-gray-200'} 
                relative inline-flex ${currentSize.switch} flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent 
                transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                ${disabled ? 'opacity-50 cursor-not-allowed' : ''}`}
            role="switch"
            aria-checked={checked}
            disabled={disabled}
            onClick={() => !disabled && onChange && onChange(!checked)}
        >
            <span className="sr-only">Toggle Status</span>
            <span
                aria-hidden="true"
                className={`${checked ? currentSize.translate : 'translate-x-0'}
                    pointer-events-none inline-block ${currentSize.dot} transform rounded-full bg-white shadow ring-0 
                    transition duration-200 ease-in-out`}
            />
        </button>
    );
}
