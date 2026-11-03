export type AdminContactMessage = {
    id: number;
    firstName: string;
    lastName: string;
    company: string | null;
    phone: string;
    email: string | null;
    message: string;
    createdAt: string;
    isRead: boolean;
};
