REPORT Organisation Management Sub System 
1. Prefix : hrom
2. Database .env Configuration : *_HR_ORGANISATION
3. Release Table : organisation, branch, contact, chart, policy
4. Changes From V1 to V2
- Field person_* & branch_* to contactable_* @contacts table
- Field tag to department @charts table
- Remove Field *_employee @charts table
- Add Field parameter @policies table
- Field type to name @policies table
- Field value to action @policies table
- Add Field code @policies table
- Add Field parameter @policies table