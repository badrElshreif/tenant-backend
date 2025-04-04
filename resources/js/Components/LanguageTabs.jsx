import React from 'react';

/**
 * Language Tabs Component for multilingual forms
 *
 * @param {Object} props - Component props
 * @param {Array} props.languages - Array of language objects with code and name
 * @param {string} props.activeTab - Currently active language code
 * @param {Function} props.onTabChange - Function to call when tab is changed
 * @returns {JSX.Element} - Rendered component
 */
export default function LanguageTabs({ languages, activeTab, onTabChange }) {
    return (
        <div className="border-b border-gray-200">
            <nav className="flex -mb-px space-x-8" aria-label="Language Tabs">
                {languages.map((lang) => (
                    <button
                        key={lang.code}
                        type="button"
                        onClick={() => onTabChange(lang.code)}
                        className={`
                            whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm
                            ${activeTab === lang.code
                                ? 'border-indigo-500 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'}
                        `}
                        aria-current={activeTab === lang.code ? 'page' : undefined}
                    >
                        {lang.name}
                    </button>
                ))}
            </nav>
        </div>
    );
}
