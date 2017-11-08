SELECT * FROM (select * from oc_product) 
as A

left join 

( select * from oc_product_special WHERE oc_product_special.date_end > '2017-11-15')  
as B

on A.product_id=B.product_id
