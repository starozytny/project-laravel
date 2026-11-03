const dateTimeFormat = new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'medium',
    timeStyle: 'short',
});

export function formatDateTime(isoDate: string): string {
    return dateTimeFormat.format(new Date(isoDate));
}
