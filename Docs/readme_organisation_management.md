REPORT Organisation Management Sub System 
1. Prefix : hrom
2. Database .env Configuration : *_HR_ORGANISATION
3. Release Table : organisation, branch, contact, chart, policy
4. Changes From V1 to V2
- Field person_* & branch_* to contactable_* @contacts table
- Field item to type @contacts table
- Field tag to department @charts table
- Remove Field *_employee @charts table
- Add Field parameter @policies table
- Field type to name @policies table
- Field value to action @policies table
- Add Field code @policies table
- Add Field parameter @policies table
5. Structure of route : inside packages, called in bootstrap
6. Problems : Observing model @the point where code of organisation is unique, ignore deleted organisation or not
7. Readable swagger