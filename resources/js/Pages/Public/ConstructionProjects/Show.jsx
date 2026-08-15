import { Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';
import Pagination from '@/Components/Pagination';

function fmt(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

const statusPillClasses = {
    completed: 'bg-brand-light-green text-brand-green',
    paused: 'bg-gray-100 text-gray-500',
    ongoing: 'bg-amber-50 text-amber-600',
};

function seoDescription(description) {
    if (!description) return undefined;
    const plain = description.replace(/<[^>]*>/g, '');
    return plain.length > 150 ? `${plain.slice(0, 150)}…` : plain;
}

export default function ConstructionProjectPublicShow({ project, transactions, statuses }) {
    return (
        <PublicLayout>
            <SeoHead
                title={`ໂຄງການກໍ່ສ້າງ: ${project.name}`}
                description={`ໂຄງການກໍ່ສ້າງ ${project.name} ວັດປ່າໜອງບົວທອງໃຕ້ — ${seoDescription(project.description)}`}
                image={project.image_url}
                keywords={`ໂຄງການກໍ່ສ້າງ, ${project.name}, ວັດປ່າໜອງບົວທອງໃຕ້, ວັດປ່າໜອງບົວທອງ, ບຸນ, ພຣະສົງ, ພຸດທະສາສະໜາ, ປະເທດລາວ, nongbuathong`}
            />

            <div className="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

                <Link href={route('construction-projects.public.index')}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-brand-green transition-colors mb-6">
                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    ໂຄງການກໍ່ສ້າງທັງໝົດ
                </Link>

                {project.image_url && (
                    <img src={project.image_url} alt={project.name}
                        className="w-full aspect-[16/9] object-cover rounded-3xl mb-8 shadow-lg shadow-black/5" />
                )}

                <div className="flex items-center gap-2 mb-2">
                    <span className={`inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold ${statusPillClasses[project.status] || 'bg-gray-50 text-gray-600'}`}>
                        {statuses[project.status] || project.status}
                    </span>
                    {project.start_date_label && <span className="text-slate-400 text-xs">ເລີ່ມ {project.start_date_label}</span>}
                </div>
                <h1 className="text-2xl sm:text-3xl font-bold text-slate-800 leading-snug mb-3">{project.name}</h1>
                {project.description ? (
                    <p className="text-slate-500 text-sm leading-relaxed max-w-2xl mb-8">{project.description}</p>
                ) : (
                    <div className="mb-8"></div>
                )}

                {/* Fundraising board */}
                <div className="bg-white rounded-2xl card-shadow border border-black/5 p-5 sm:p-6 mb-10">
                    {project.progress_percent !== null ? (
                        <>
                            <div className="flex items-baseline justify-between mb-2">
                                <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລະດົມທຶນໄດ້</span>
                                <span className="text-xl font-extrabold text-brand-green tabular-nums">{project.progress_percent}%</span>
                            </div>
                            <div className="relative h-3.5 rounded-full bg-brand-light-green mb-2">
                                <div className="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#ffb366] to-brand-green transition-all duration-700 ease-out"
                                    style={{ width: `${project.progress_percent}%` }}></div>
                                <div className="absolute inset-y-0 left-1/4 w-px bg-white/60"></div>
                                <div className="absolute inset-y-0 left-1/2 w-px bg-white/60"></div>
                                <div className="absolute inset-y-0 left-3/4 w-px bg-white/60"></div>
                                <div className="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-white border-2 border-brand-green shadow-sm"
                                    style={{ left: `${project.progress_percent}%` }}></div>
                            </div>
                            <p className="text-xs text-gray-400 mb-5">{fmt(project.total_income)} / {fmt(project.target_amount)} ກີບ · ເປົ້າໝາຍລະດົມທຶນ</p>
                            <div className="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                                <div>
                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ໄດ້ຮັບ</p>
                                    <p className="text-sm font-bold text-brand-green tabular-nums">{fmt(project.total_income)}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຈ່າຍແລ້ວ</p>
                                    <p className="text-sm font-bold text-rose-800 tabular-nums">{fmt(project.total_expense)}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຍອດເຫຼືອ</p>
                                    <p className={`text-sm font-bold tabular-nums ${project.balance >= 0 ? 'text-slate-800' : 'text-rose-800'}`}>{fmt(project.balance)}</p>
                                </div>
                            </div>
                        </>
                    ) : (
                        <div className="grid grid-cols-3 gap-4">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ໄດ້ຮັບ</p>
                                <p className="text-base font-bold text-brand-green tabular-nums">{fmt(project.total_income)}</p>
                            </div>
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຈ່າຍແລ້ວ</p>
                                <p className="text-base font-bold text-rose-800 tabular-nums">{fmt(project.total_expense)}</p>
                            </div>
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຍອດເຫຼືອ</p>
                                <p className={`text-base font-bold tabular-nums ${project.balance >= 0 ? 'text-slate-800' : 'text-rose-800'}`}>{fmt(project.balance)}</p>
                            </div>
                        </div>
                    )}
                </div>

                {/* Ledger */}
                <div className="flex items-center gap-3 mb-5">
                    <p className="text-sm font-bold text-slate-800 shrink-0">ບັນຊີລາຍຮັບ-ລາຍຈ່າຍ</p>
                    <div className="flex-1 h-px bg-gray-200"></div>
                    <span className="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full shrink-0">
                        {transactions.total} ລາຍການ
                    </span>
                </div>

                {transactions.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center text-center py-16 bg-white rounded-2xl card-shadow border border-black/5">
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີລາຍການ</p>
                        <p className="text-slate-400 text-sm">ໂຄງການນີ້ຍັງບໍ່ທັນມີການບັນທຶກລາຍຮັບ-ລາຍຈ່າຍ</p>
                    </div>
                ) : (
                    <>
                        <div className="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden">
                            {transactions.data.map((tx, i) => (
                                <div key={tx.id} className={`flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-4 ${i !== transactions.data.length - 1 ? 'border-b border-gray-100' : ''}`}>
                                    <div className="shrink-0 w-10 text-center">
                                        <p className="text-[9px] font-bold uppercase tracking-wide text-gray-400 leading-none mb-0.5">{tx.transaction_date_month}</p>
                                        <p className="text-base font-extrabold text-slate-800 leading-none tabular-nums">{tx.transaction_date_day}</p>
                                    </div>

                                    {tx.image_url ? (
                                        <a href={tx.image_url} target="_blank" rel="noopener noreferrer"
                                            className="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                                            <img src={tx.image_url} alt="ຮູບບິນ" className="w-full h-full object-cover" />
                                        </a>
                                    ) : (
                                        <div className="w-12 h-12 rounded-xl bg-[#f8fafa] border border-gray-100 shrink-0 flex items-center justify-center">
                                            <svg className="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    )}

                                    <div className="flex-1 min-w-0">
                                        <p className="font-semibold text-slate-800 text-sm truncate">{tx.description || (tx.type === 'income' ? 'ລາຍຮັບ' : 'ລາຍຈ່າຍ')}</p>
                                    </div>

                                    <p className={`shrink-0 font-extrabold tabular-nums text-sm sm:text-base ${tx.type === 'income' ? 'text-brand-green' : 'text-rose-800'}`}>
                                        {tx.type === 'income' ? '+' : '−'}{fmt(tx.amount)}
                                    </p>
                                </div>
                            ))}
                        </div>

                        {transactions.data.length > 0 && <div className="mt-6"><Pagination links={transactions.links} /></div>}
                    </>
                )}
            </div>
        </PublicLayout>
    );
}
