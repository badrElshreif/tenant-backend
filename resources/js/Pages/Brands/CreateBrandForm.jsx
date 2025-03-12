import {Head} from "@inertiajs/react";
import {routeTenant} from "@/app.jsx";
import React, {useState} from 'react';
import {router} from '@inertiajs/react';


export default function CreateBrandForm({onClose, successCallback}) {
    const [formData, setFormData] = useState({
        name: '',
        logo: null,
        status: true,
        description: ''
    });

    const [errors, setErrors] = useState({});
    const [processing, setProcessing] = useState(false);
    const [logoPreview, setLogoPreview] = useState(null);

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
        } else {
            setFormData(prev => ({
                ...prev,
                [name]: type === 'checkbox' ? checked : value
            }));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);

        // Create FormData object for file upload
        const submitData = new FormData();
        for (const key in formData) {
            submitData.append(key, formData[key]);
        }

        router.post('/brands', submitData, {
            onSuccess: () => {
                setProcessing(false);
                resetForm();
                if (successCallback) successCallback();
                onClose();
            },
            onError: (errors) => {
                setProcessing(false);
                setErrors(errors);
            }
        });
    };

    const resetForm = () => {
        setFormData({
            name: '',
            logo: null,
            status: true,
            description: ''
        });
        setLogoPreview(null);
        setErrors({});
    };

    return (
        <form onSubmit={handleSubmit}>
            <div className="space-y-4">
                <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                        Brand Name *
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value={formData.name}
                        onChange={handleChange}
                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ${errors.name ? 'border-red-500' : ''}`}
                        placeholder="Enter brand name"
                        required
                    />
                    {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                </div>

                <div>
                    <label htmlFor="logo" className="block text-sm font-medium text-gray-700">
                        Brand Logo
                    </label>
                    <div className="mt-1 flex items-center space-x-4">
                        <div className="flex-shrink-0">
                            {logoPreview ? (
                                <img src={logoPreview} alt="Logo preview"
                                     className="h-16 w-16 rounded-full object-cover"/>
                            ) : (
                                <div className="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span className="text-gray-400 text-xs">No logo</span>
                                </div>
                            )}
                        </div>
                        <div className="flex-1">
                            <label
                                htmlFor="logo-upload"
                                className="relative cursor-pointer rounded-md bg-white py-2 px-3 border border-gray-300 shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                            >
                                <span>Upload logo</span>
                                <input
                                    id="logo-upload"
                                    name="logo"
                                    type="file"
                                    className="sr-only"
                                    accept="image/*"
                                    onChange={handleChange}
                                />
                            </label>
                        </div>
                    </div>
                    {errors.logo && <p className="mt-1 text-sm text-red-600">{errors.logo}</p>}
                </div>

                <div>
                    <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows={3}
                        value={formData.description}
                        onChange={handleChange}
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Brand description (optional)"
                    />
                    {errors.description && <p className="mt-1 text-sm text-red-600">{errors.description}</p>}
                </div>

                <div className="flex items-center">
                    <input
                        id="status"
                        name="status"
                        type="checkbox"
                        checked={formData.status}
                        onChange={handleChange}
                        className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <label htmlFor="status" className="ml-2 block text-sm text-gray-700">
                        Active Status
                    </label>
                </div>
            </div>

            <div className="mt-6 flex justify-end space-x-3">
                <button
                    type="button"
                    className="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    onClick={() => {
                        resetForm();
                        onClose();
                    }}
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    className="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    disabled={processing}
                >
                    {processing ? 'Saving...' : 'Save Brand'}
                </button>
            </div>
        </form>
    );
}
