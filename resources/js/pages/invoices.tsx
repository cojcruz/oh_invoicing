import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { invoices } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Invoices',
        href: invoices().url,
    },
];

const invoiceItems: any = [];

export default function Invoices() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Invoices" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex flex-col gap-2 grid justify-content-end md:grid-cols-4">
                    <button className="btn btn-primary"><a href="#">Create Invoice</a></button>
                    <button className="btn btn-secondary"><a href="#">Edit Invoice</a></button>
                </div>                
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <table className="w-full border-collapse table-auto">
                        <thead>
                            <tr>
                                <th className="px-4 py-2">Select</th>
                                <th className="px-4 py-2">Invoice Number</th>
                                <th className="px-4 py-2">Employee</th>
                                <th className="px-4 py-2">Team</th>
                                <th className="px-4 py-2">Date</th>
                                <th className="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {/* Loop through the invoices array and create a new row for each invoice */}
                            {invoiceItems.map((invoiceItem) => (
                                <tr key={invoiceItem.id}>
                                    <td className="px-4 py-2"><input type="checkbox" /></td>
                                    <td className="px-4 py-2">{invoiceItem.invoice_number}</td>
                                    <td className="px-4 py-2">{invoiceItem.employee}</td>
                                    <td className="px-4 py-2">{invoiceItem.team}</td>
                                    <td className="px-4 py-2">{invoiceItem.date}</td>
                                    <td className="px-4 py-2">
                                        <button className="btn btn-secondary"><a href={`/invoices/${invoiceItem.id}/edit`}>Edit</a></button>
                                        <button className="btn btn-error"><a href={`/invoices/${invoiceItem.id}/delete`}>Delete</a></button>
                                        <button className="btn btn-primary"><a href={`/invoices/${invoiceItem.id}/duplicate`}>Duplicate</a></button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
                <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
                <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </AppLayout>
    );
}
