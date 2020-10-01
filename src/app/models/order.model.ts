export interface Order {
address_id: number
user_id: number
order_id: number
product_id: number
quantity: number
delivered: 1 | 0
state: string
pin: number
house: string
area: string
landmark: string
town: string
company: string
name: string
category: string
description: string
price: string
image: string
available: 1 | 0
date: Date

}