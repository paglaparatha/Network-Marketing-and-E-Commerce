export interface User {
    id: number;
    name: string;
    dob: Date;
    email: string;
    password: string;
    image: string;
    aadhaar: string;
    my_ref: string;
    super_ref: string;
    company: string;
    mobile: string;

    all_underlings: User[];
    immediate_underlings: User[];
    immediate_underlings_count: number;
    income: number;
    company_img: string;
}