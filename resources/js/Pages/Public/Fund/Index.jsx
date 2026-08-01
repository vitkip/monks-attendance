import { Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';
import Pagination from '@/Components/Pagination';
import CopyAccountButton from '@/Components/CopyAccountButton';

function fmt(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

const FILTERS = [
    ['', 'ທັງໝົດ'],
    ['income', 'ລາຍຮັບ'],
    ['expense', 'ລາຍຈ່າຍ'],
];

export default function FundPublicIndex({ type, transactions, totalIncomeAll, totalExpenseAll, balanceAll, fundAccount }) {
    const hasAccount = fundAccount && (fundAccount.bank_name || fundAccount.account_name || fundAccount.account_number);
    return (
        <PublicLayout>
            <SeoHead
                title="ກອງທຶນ"
                description="ຄວາມໂປ່ງໃສລາຍຮັບ-ລາຍຈ່າຍຂອງກອງທຶນວັດປ່າໜອງບົວທອງໃຕ້"
            />

            {/* Hero */}
            <section className="relative overflow-hidden bg-brand-green-dark">
                <span
                    className="absolute -right-6 -top-14 text-[240px] leading-none text-white/[0.04] select-none pointer-events-none"
                    aria-hidden="true">💰</span>

                <div className="relative max-w-6xl mx-auto px-5 sm:px-8 pt-16 pb-10 sm:pt-20 sm:pb-12">
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                        <span aria-hidden="true">💰</span> ຄວາມໂປ່ງໃສທາງການເງິນ
                    </span>

                    <h1 className="text-white text-3xl sm:text-4xl font-bold leading-tight">ບັນຊີກອງທຶນດອກບົວທອງ</h1>
                    <p className="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                        ເປີດເຜີຍລາຍຮັບ-ລາຍຈ່າຍຂອງກອງທຶນວັດ ໃຫ້ຍາດໂຍມ ແລະ ຜູ້ມີສັດທາຮ່ວມຕິດຕາມໄດ້
                    </p>

                    <div className="flex flex-wrap items-center gap-3 mt-7">
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-white tabular-nums leading-none">{fmt(totalIncomeAll)}</span>
                            <span className="text-xs text-white/60">ກີບ · ລາຍຮັບທັງໝົດ</span>
                        </div>
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-white tabular-nums leading-none">{fmt(totalExpenseAll)}</span>
                            <span className="text-xs text-white/60">ກີບ · ລາຍຈ່າຍທັງໝົດ</span>
                        </div>
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{fmt(balanceAll)}</span>
                            <span className="text-xs text-white/60">ກີບ · ຍອດເຫຼືອສຸດທິ</span>
                        </div>
                    </div>

                    {/* Donate — the account details visitors actually came here for */}
                    {hasAccount && (
                        <div className="mt-9 bg-white/[0.04] rounded-2xl px-5 sm:px-6 py-6">
                            <p className="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-4">ຮ່ວມບໍລິຈາກເຂົ້າກອງທຶນ</p>
                            <div className="flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-8">
                                {fundAccount.bank_name && (
                                    <div>
                                        <p className="text-[10px] text-white/40 uppercase tracking-widest mb-1">ທະນາຄານ</p>
                                        <p className="text-white text-sm font-semibold">{fundAccount.bank_name}</p>
                                    </div>
                                )}
                                {fundAccount.account_name && (
                                    <div>
                                        <p className="text-[10px] text-white/40 uppercase tracking-widest mb-1">ຊື່ບັນຊີ</p>
                                        <p className="text-white text-sm font-semibold">{fundAccount.account_name}</p>
                                    </div>
                                )}
                                {fundAccount.account_number && (
                                    <div>
                                        <p className="text-[10px] text-white/40 uppercase tracking-widest mb-1">ເລກບັນຊີ</p>
                                        <CopyAccountButton
                                            value={fundAccount.account_number}
                                            className="text-lg font-extrabold text-white hover:text-brand-bright-green"
                                            iconClassName="w-4 h-4"
                                        />
                                    </div>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </section>

            <div className="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

                {/* Type filter */}
                <nav aria-label="ເລືອກປະເພດ"
                    className="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5">
                    <div className="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                        {FILTERS.map(([value, label]) => (
                            <Link
                                key={value}
                                href={value ? route('fund.public.index', { type: value }) : route('fund.public.index')}
                                preserveScroll
                                className={`shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                                          ${type === value ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50'}`}>
                                {label}
                            </Link>
                        ))}
                    </div>
                </nav>

                {transactions.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-brand-green/30 mb-4">💰</span>
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີລາຍການ</p>
                        <p className="text-slate-400 text-sm">ກອງທຶນຍັງບໍ່ທັນມີການບັນທຶກລາຍຮັບ-ລາຍຈ່າຍ</p>
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

                                    <div className="shrink-0 w-11 h-11 rounded-full overflow-hidden bg-[#f8fafa] border border-gray-100 flex items-center justify-center">
                                        {tx.monk ? (
                                            <img src={tx.monk.photo_url} alt={tx.monk.full_name} className="w-full h-full object-cover" />
                                        ) : (
                                            <svg className="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        )}
                                    </div>

                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-1.5">
                                            <p className="font-semibold text-slate-800 text-sm truncate">{tx.party_label}</p>
                                            {tx.monk && <span className="shrink-0 text-[10px] font-semibold text-brand-green bg-brand-light-green px-1.5 py-0.5 rounded-md">{tx.monk.type_label}</span>}
                                        </div>
                                        <p className="text-gray-400 text-xs truncate">{tx.description || (tx.type === 'income' ? 'ລາຍຮັບ' : 'ລາຍຈ່າຍ')}</p>
                                    </div>

                                    <p className={`shrink-0 font-extrabold tabular-nums text-sm sm:text-base ${tx.type === 'income' ? 'text-brand-green' : 'text-rose-800'}`}>
                                        {tx.type === 'income' ? '+' : '−'}{fmt(tx.amount)}
                                    </p>
                                </div>
                            ))}
                        </div>

                        {transactions.data.length > 0 && <div className="mt-8"><Pagination links={transactions.links} /></div>}
                    </>
                )}
            </div>
        </PublicLayout>
    );
}
