import {Head} from "@inertiajs/react";
import {routeTenant} from "@/app.jsx";
import React, {useState, useEffect} from 'react';
import {router} from '@inertiajs/react';
import LanguageTabs from "@/Components/LanguageTabs";
import Toast from "@/Components/Toast";

export default function CreateBrandForm({onClose, successCallback, brand = null}) {
    const isEditMode = !!brand;
    const [activeTab, setActiveTab] = useState('en');
    const languages = [
        { code: 'en', name: 'English' },
        { code: 'ar', name: 'Arabic' }
    ];

    const [formData, setFormData] = useState({
        translations: {
            en: { name: '', description: '' },
            ar: { name: '', description: '' }
        },
        logo: null,
        status: true
    });

    const [errors, setErrors] = useState({});
    const [processing, setProcessing] = useState(false);
    const [logoPreview, setLogoPreview] = useState(null);
    const [toast, setToast] = useState({
        show: false,
        message: '',
        type: 'success'
    });

    // Initialize form with brand data if in edit mode
    useEffect(() => {
        if (isEditMode && brand) {
            // Extract translations from brand data
            const translations = {
                en: { name: '', description: '' },
                ar: { name: '', description: '' }
            };

            // Handle translations from the brand resource
            if (brand.translations) {
                translations.en = {
                    name: brand.translations.en?.name || '',
                    description: brand.translations.en?.description || ''
                };
                translations.ar = {
                    name: brand.translations.ar?.name || '',
                    description: brand.translations.ar?.description || ''
                };
            } else {
                // Fallback to direct properties if translations key doesn't exist
                translations.en = {
                    name: brand.en?.name || '',
                    description: brand.en?.description || ''
                };
                translations.ar = {
                    name: brand.ar?.name || '',
                    description: brand.ar?.description || ''
                };
            }

            const initialFormData = {
                translations: translations,
                logo: null, // File inputs can't be pre-filled for security reasons
                status: brand.is_active
            };

            setFormData(initialFormData);

            // Set logo preview if available
            if (brand.image) {
                setLogoPreview(brand.image);
            }
        }
    }, [brand, isEditMode]);

    const handleChange = (e) => {
        const {name, value, type, checked, files} = e.target;

        if (type === 'file') {
            const file = files[0];
            setFormData(prev => ({
                ...prev,
                [name]: file
            }));

            // Create preview for image
            if (file) {
                const reader = new FileReader();
                reader.onloadend = () => {
                    setLogoPreview(reader.result);
                };
                reader.readAsDataURL(file);
            }
        } else if (type === 'checkbox') {
            setFormData(prev => ({
                ...prev,
                [name]: checked
            }));
        } else if (['name', 'description'].includes(name)) {
            // Handle translation fields
            setFormData(prev => ({
                ...prev,
                translations: {
                    ...prev.translations,
                    [activeTab]: {
                        ...prev.translations[activeTab],
                        [name]: value
                    }
                }
            }));
        } else {
            setFormData(prev => ({
                ...prev,
                [name]: value
            }));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);

        // Create FormData object for file upload
        const submitData = new FormData();

        // Add translations data
        for (const lang of languages) {
            const langCode = lang.code;
            submitData.append(`translations[${langCode}][name]`, formData.translations[langCode].name);
            submitData.append(`translations[${langCode}][description]`, formData.translations[langCode].description);
        }

        // Add other form data
        if (formData.logo) {
            submitData.append('logo', formData.logo);
        }
        submitData.append('status', formData.status ? 1 : 0);

        // If editing, use PATCH method and update endpoint
        if (isEditMode) {
            submitData.append('_method', 'PATCH'); // Laravel method spoofing

            router.post(routeTenant('tenant.dashboard.brands.update', {
                tenant: route().params.tenant,
                brand: brand.id
            }), submitData, {
                onSuccess: (response) => {
                    setProcessing(false);

                    // Call the success callback from parent component
                    if (successCallback) {
                        successCallback(response.brand);
                    }

                    // Close the modal
                    if (onClose) {
                        onClose();
                    }
                },
                onError: (errors) => {
                    setProcessing(false);
                    setErrors(errors);

                    // Show error toast
                    setToast({
                        show: true,
                        message: 'Failed to update brand. Please check the form for errors.',
                        type: 'error'
                    });
                }
            });
        } else {
            // Create new brand
            router.post(routeTenant('tenant.dashboard.brands.store', {
                tenant: route().params.tenant
            }), submitData, {
                onSuccess: (response) => {
                    setProcessing(false);
                    resetForm();

                    // Call the success callback from parent component
                    if (successCallback) {
                        successCallback(response.brand);
                    }

                    // Close the modal
                    if (onClose) {
                        onClose();
                    }
                },
                onError: (errors) => {
                    setProcessing(false);
                    setErrors(errors);

                    // Show error toast
                    setToast({
                        show: true,
                        message: 'Failed to create brand. Please check the form for errors.',
                        type: 'error'
                    });
                }
            });
        }
    };

    const resetForm = () => {
        setFormData({
            translations: {
                en: { name: '', description: '' },
                ar: { name: '', description: '' }
            },
            logo: null,
            status: true
        });
        setLogoPreview(null);
        setErrors({});
        setActiveTab('en');
    };

    return (
        <>
            <Toast
                show={toast.show}
                message={toast.message}
                type={toast.type}
                onClose={() => setToast({...toast, show: false})}
            />

            <form onSubmit={handleSubmit}>
                <div className="space-y-4">
                    {/* Language Tabs */}
                    <LanguageTabs
                        languages={languages}
                        activeTab={activeTab}
                        onTabChange={setActiveTab}
                    />

                    {/* Form Fields for Current Language */}
                    <div className="space-y-4">
                        <div>
                            <label htmlFor={`name-${activeTab}`} className="block text-sm font-medium text-gray-700">
                                Brand Name ({activeTab === 'en' ? 'English' : 'Arabic'})
                            </label>
                            <input
                                type="text"
                                name="name"
                                id={`name-${activeTab}`}
                                value={formData.translations[activeTab].name}
                                onChange={handleChange}
                                className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ${
                                    errors[`translations.${activeTab}.name`] ? 'border-red-500' : ''
                                }`}
                                placeholder={`Enter brand name in ${activeTab === 'en' ? 'English' : 'Arabic'}`}
                                required={activeTab === 'en'} // English is required, other languages optional
                                dir={activeTab === 'ar' ? 'rtl' : 'ltr'} // Right-to-left for Arabic
                            />
                            {errors[`translations.${activeTab}.name`] && (
                                <p className="mt-1 text-sm text-red-600">{errors[`translations.${activeTab}.name`]}</p>
                            )}
                        </div>

                        <div>
                            <label htmlFor={`description-${activeTab}`} className="block text-sm font-medium text-gray-700">
                                Description ({activeTab === 'en' ? 'English' : 'Arabic'})
                            </label>
                            <textarea
                                id={`description-${activeTab}`}
                                name="description"
                                rows={3}
                                value={formData.translations[activeTab].description}
                                onChange={handleChange}
                                className="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder={`Brand description in ${activeTab === 'en' ? 'English' : 'Arabic'} (optional)`}
                                dir={activeTab === 'ar' ? 'rtl' : 'ltr'} // Right-to-left for Arabic
                            />
                            {errors[`translations.${activeTab}.description`] && (
                                <p className="mt-1 text-sm text-red-600">{errors[`translations.${activeTab}.description`]}</p>
                            )}
                        </div>
                    </div>

                    {/* Logo Upload */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">Brand Logo</label>
                        <div className="flex items-center mt-1 space-x-5">
                            <div className="flex-shrink-0">
                                {logoPreview ? (
                                    <img
                                        src={logoPreview}
                                        alt="Logo Preview"
                                        className="object-cover w-16 h-16 rounded-md"
                                    />
                                ) : (
                                    <div className="flex justify-center items-center w-16 h-16 bg-gray-100 rounded-md">
                                        <svg
                                            className="w-12 h-12 text-gray-300"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                )}
                            </div>
                            <div className="flex-1">
                                <input
                                    type="file"
                                    name="logo"
                                    id="logo"
                                    accept="image/*"
                                    onChange={handleChange}
                                    className="sr-only"
                                />
                                <label
                                    htmlFor="logo"
                                    className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 shadow-sm cursor-pointer hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Upload Logo
                                </label>
                                {errors.logo && (
                                    <p className="mt-1 text-sm text-red-600">{errors.logo}</p>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Active Status */}
                    <div className="flex items-center">
                        <input
                            id="status"
                            name="status"
                            type="checkbox"
                            checked={formData.status}
                            onChange={handleChange}
                            className="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                        />
                        <label htmlFor="status" className="block ml-2 text-sm text-gray-900">
                            Active
                        </label>
                    </div>

                    {/* Form Actions */}
                    <div className="flex justify-end pt-5 space-x-3">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className={`px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ${
                            processing ? 'opacity-75 cursor-not-allowed' : ''}`}
                        >
                            {processing ? 'Saving...' : isEditMode ? 'Update Brand' : 'Save Brand'}
                        </button>
                    </div>
                </div>
            </form>
        </>
    );
}
