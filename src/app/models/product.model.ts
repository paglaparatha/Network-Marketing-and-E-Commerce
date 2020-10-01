export interface Product {
    id: number;
    name: string;
    category: string;
    description: string;
    price: number;
    image: string;
    available: 1 | 0;
    loaded: boolean;
    company: string
}