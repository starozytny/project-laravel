export type AdminUser = {
    id: number;
    name: string;
    email: string;
    roles: { value: string, label: string } [];
};
