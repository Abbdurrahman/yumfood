<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

## instalation guide

1. run composer install
2. run php artisan migrate

# sample for query filtering
`api/v1/vendors?tag=name_vendor'

# sample request for an save or update order
{
    "vendor_id" : 1,
    "tag_id" : [1,2,3],
    "qty" : [1,1,1],
    "note" : ["Tambahan Saussss","Tambahan Bawang",""]
}
