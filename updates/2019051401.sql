

alter table invoice__invoice add column credit_invoice boolean default false after invoice_status_id;
alter table invoice__invoice add column ref_invoice_id int default null after invoice_id;

