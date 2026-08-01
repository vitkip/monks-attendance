import { useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function SettingsIndex({ currentLogo, contactWhatsapp, contactFacebook, contactEmail, contactYoutube }) {
    const [dragging, setDragging] = useState(false);
    const [logoPreview, setLogoPreview] = useState('');

    const logoForm = useForm({ logo: null });
    const contactForm = useForm({
        contactWhatsapp: contactWhatsapp || '',
        contactFacebook: contactFacebook || '',
        contactEmail: contactEmail || '',
        contactYoutube: contactYoutube || '',
    });

    function pickLogoFile(file) {
        if (!file) return;
        logoForm.setData('logo', file);
        setLogoPreview(URL.createObjectURL(file));
    }

    function onLogoInputChange(e) {
        pickLogoFile(e.target.files?.[0] || null);
    }

    function onDrop(e) {
        e.preventDefault();
        setDragging(false);
        pickLogoFile(e.dataTransfer.files?.[0] || null);
    }

    function submitLogo(e) {
        e.preventDefault();
        logoForm.post(route('settings.logo.update'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                logoForm.reset();
                setLogoPreview('');
            },
        });
    }

    function removeLogo() {
        if (!window.confirm('ທ່ານຕ້ອງການລຶບ logo ນີ້ບໍ?')) return;
        router.post(route('settings.logo.remove'), {}, { preserveScroll: true });
    }

    function submitContact(e) {
        e.preventDefault();
        contactForm.post(route('settings.contact.update'), { preserveScroll: true });
    }

    return (
        <AppLayout title="ຕັ້ງຄ່າລະບົບ">
            <div className="max-w-xl mx-auto space-y-6">

                {/* Header */}
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຕັ້ງຄ່າ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ຕັ້ງຄ່າລະບົບ</h1>
                    <p className="text-gray-400 text-sm mt-1">ອັບໂຫລດ logo ແລະ ຂໍ້ມູນຕິດຕໍ່ສຳລັບໜ້າຫລັກ</p>
                </div>

                {/* Logo Card */}
                <div className="bg-white rounded-3xl card-shadow overflow-hidden">

                    <div className="px-6 py-4 border-b border-gray-100 bg-[#f8fafa]">
                        <h2 className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Logo ລະບົບ</h2>
                    </div>

                    <div className="px-6 py-6 space-y-5">

                        {/* Current logo preview */}
                        {currentLogo ? (
                            <div className="flex items-start gap-4">
                                <div className="flex-shrink-0 w-20 h-20 rounded-2xl border border-gray-200 bg-[#f8fafa] flex items-center justify-center overflow-hidden">
                                    <img src={`/storage/${currentLogo}`} alt="Logo" className="w-full h-full object-contain p-1" />
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-medium text-slate-800">Logo ປັດຈຸບັນ</p>
                                    <p className="text-xs text-gray-400 mt-0.5 break-all">{currentLogo.split('/').pop()}</p>
                                    <button
                                        type="button"
                                        onClick={removeLogo}
                                        className="mt-2 text-xs text-red-500 hover:text-red-600 font-medium transition-colors"
                                    >
                                        ລຶບ logo
                                    </button>
                                </div>
                            </div>
                        ) : (
                            <div className="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200">
                                <span className="text-2xl select-none">☸️</span>
                                <p className="text-sm text-gray-400">ກຳລັງໃຊ້ icon ເລີ່ມຕົ້ນ (☸️) — ອັບໂຫລດ logo ເພື່ອປ່ຽນ</p>
                            </div>
                        )}

                        {/* Upload form */}
                        <form onSubmit={submitLogo} className="space-y-4">

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                                    {currentLogo ? 'ປ່ຽນ Logo ໃໝ່' : 'ອັບໂຫລດ Logo'}
                                </label>

                                <div
                                    onDragOver={(e) => { e.preventDefault(); setDragging(true); }}
                                    onDragLeave={(e) => { e.preventDefault(); setDragging(false); }}
                                    onDrop={onDrop}
                                    className={`relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer ${
                                        dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'
                                    }`}
                                >
                                    <input
                                        type="file"
                                        accept="image/*"
                                        onChange={onLogoInputChange}
                                        className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    />

                                    <div className="flex flex-col items-center justify-center py-8 px-4 text-center pointer-events-none">
                                        <svg className="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        <p className="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span className="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                                        <p className="text-xs text-gray-400/70 mt-1">PNG, JPG, SVG, GIF — ສູງສຸດ 2MB</p>
                                    </div>
                                </div>

                                {logoForm.errors.logo && (
                                    <p className="mt-1.5 text-red-500 text-xs">{logoForm.errors.logo}</p>
                                )}
                            </div>

                            {/* Preview of newly selected file */}
                            {logoForm.data.logo && (
                                <div className="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200">
                                    <img src={logoPreview} alt="Preview" className="w-12 h-12 object-contain rounded-lg border border-gray-200" />
                                    <div className="min-w-0">
                                        <p className="text-xs font-medium text-slate-800">{logoForm.data.logo.name}</p>
                                        <p className="text-xs text-gray-400">{(logoForm.data.logo.size / 1024).toFixed(1)} KB</p>
                                    </div>
                                </div>
                            )}

                            <div className="flex justify-end">
                                <button
                                    type="submit"
                                    disabled={logoForm.processing}
                                    className="px-5 py-2.5 bg-brand-green hover:bg-opacity-90 text-white text-sm font-semibold rounded-2xl transition shadow-lg shadow-brand-green/20 disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    {logoForm.processing ? 'ກຳລັງອັບໂຫລດ...' : 'ບັນທຶກ'}
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

                {/* Contact Info Card */}
                <div className="bg-white rounded-3xl card-shadow overflow-hidden">

                    <div className="px-6 py-4 border-b border-gray-100 bg-[#f8fafa]">
                        <h2 className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ຂໍ້ມູນຕິດຕໍ່</h2>
                    </div>

                    <div className="px-6 py-6">
                        <form onSubmit={submitContact} className="space-y-4">

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">WhatsApp</label>
                                <input
                                    type="text"
                                    value={contactForm.data.contactWhatsapp}
                                    onChange={(e) => contactForm.setData('contactWhatsapp', e.target.value)}
                                    placeholder="ຕົວຢ່າງ: 8562012345678"
                                    className="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-[#f8fafa] text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green"
                                />
                                {contactForm.errors.contactWhatsapp && (
                                    <p className="mt-1.5 text-red-500 text-xs">{contactForm.errors.contactWhatsapp}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">Facebook</label>
                                <input
                                    type="url"
                                    value={contactForm.data.contactFacebook}
                                    onChange={(e) => contactForm.setData('contactFacebook', e.target.value)}
                                    placeholder="https://facebook.com/..."
                                    className="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-[#f8fafa] text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green"
                                />
                                {contactForm.errors.contactFacebook && (
                                    <p className="mt-1.5 text-red-500 text-xs">{contactForm.errors.contactFacebook}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ອີເມວ</label>
                                <input
                                    type="email"
                                    value={contactForm.data.contactEmail}
                                    onChange={(e) => contactForm.setData('contactEmail', e.target.value)}
                                    placeholder="example@email.com"
                                    className="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-[#f8fafa] text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green"
                                />
                                {contactForm.errors.contactEmail && (
                                    <p className="mt-1.5 text-red-500 text-xs">{contactForm.errors.contactEmail}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">YouTube</label>
                                <input
                                    type="url"
                                    value={contactForm.data.contactYoutube}
                                    onChange={(e) => contactForm.setData('contactYoutube', e.target.value)}
                                    placeholder="https://youtube.com/..."
                                    className="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-[#f8fafa] text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green"
                                />
                                {contactForm.errors.contactYoutube && (
                                    <p className="mt-1.5 text-red-500 text-xs">{contactForm.errors.contactYoutube}</p>
                                )}
                            </div>

                            <div className="flex justify-end">
                                <button
                                    type="submit"
                                    disabled={contactForm.processing}
                                    className="px-5 py-2.5 bg-brand-green hover:bg-opacity-90 text-white text-sm font-semibold rounded-2xl transition shadow-lg shadow-brand-green/20 disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    {contactForm.processing ? 'ກຳລັງບັນທຶກ...' : 'ບັນທຶກ'}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </AppLayout>
    );
}
