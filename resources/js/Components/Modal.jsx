// Modal.jsx
import React, { Fragment } from 'react';
import { Dialog, Transition } from '@headlessui/react';
import { XMarkIcon } from '@heroicons/react/24/outline';

export default function Modal({
                                  isOpen,
                                  onClose,
                                  title,
                                  children,
                                  maxWidth = 'md', // options: sm, md, lg, xl, 2xl, etc.
                                  fullWidth = false,
                                  fullHeight = false
                              }) {
    const maxWidthClasses = {
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg',
        xl: 'max-w-xl',
        '2xl': 'max-w-2xl',
        '3xl': 'max-w-3xl',
        '4xl': 'max-w-4xl',
        '5xl': 'max-w-5xl',
        'full': 'max-w-full'
    };

    // Determine width class
    const widthClass = fullWidth ? 'max-w-full w-full' : maxWidthClasses[maxWidth] || maxWidthClasses.md;

    return (
        <Transition appear show={isOpen} as={Fragment}>
            <Dialog as="div" className="relative z-50" onClose={onClose}>
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="ease-in duration-200"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                >
                    <div className="fixed inset-0 bg-black bg-opacity-25" />
                </Transition.Child>

                {fullHeight ? (
                    // Full height (full screen) modal implementation
                    <div className="fixed inset-0">
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0 scale-95"
                            enterTo="opacity-100 scale-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100 scale-100"
                            leaveTo="opacity-0 scale-95"
                        >
                            <Dialog.Panel
                                className="h-full w-full transform bg-white shadow-xl transition-all flex flex-col"
                            >
                                <div className="flex items-start justify-between border-b border-gray-200 px-6 py-4">
                                    {title && (
                                        <Dialog.Title
                                            as="h3"
                                            className="text-lg font-medium leading-6 text-gray-900"
                                        >
                                            {title}
                                        </Dialog.Title>
                                    )}
                                    <button
                                        type="button"
                                        className="ml-auto inline-flex justify-center rounded-md border border-transparent bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none"
                                        onClick={onClose}
                                    >
                                        <span className="sr-only">Close</span>
                                        <XMarkIcon className="h-5 w-5" aria-hidden="true" />
                                    </button>
                                </div>
                                <div className="flex-1 p-6 overflow-auto">
                                    {children}
                                </div>
                            </Dialog.Panel>
                        </Transition.Child>
                    </div>
                ) : (
                    // Regular modal with configurable width
                    <div className="fixed inset-0 overflow-y-auto">
                        <div className="flex min-h-full items-center justify-center p-4 text-center">
                            <Transition.Child
                                as={Fragment}
                                enter="ease-out duration-300"
                                enterFrom="opacity-0 scale-95"
                                enterTo="opacity-100 scale-100"
                                leave="ease-in duration-200"
                                leaveFrom="opacity-100 scale-100"
                                leaveTo="opacity-0 scale-95"
                            >
                                <Dialog.Panel
                                    className={`w-full ${widthClass} transform overflow-hidden rounded-lg bg-white p-6 text-left align-middle shadow-xl transition-all`}
                                >
                                    <div className="flex items-start justify-between">
                                        {title && (
                                            <Dialog.Title
                                                as="h3"
                                                className="text-lg font-medium leading-6 text-gray-900"
                                            >
                                                {title}
                                            </Dialog.Title>
                                        )}
                                        <button
                                            type="button"
                                            className="ml-auto inline-flex justify-center rounded-md border border-transparent bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none"
                                            onClick={onClose}
                                        >
                                            <span className="sr-only">Close</span>
                                            <XMarkIcon className="h-5 w-5" aria-hidden="true" />
                                        </button>
                                    </div>
                                    <div className="mt-4">
                                        {children}
                                    </div>
                                </Dialog.Panel>
                            </Transition.Child>
                        </div>
                    </div>
                )}
            </Dialog>
        </Transition>
    );
}
