REPORT Employment System Sub System 
1. Prefix : hres
2. Database .env Configuration : *_HR_EMPLOYEE
3. Release Table : contract_elements, contracts_works, works, grade_logs
4. Changes From V1 to V2
- Field grade @works table to grade_logs new table
- Add Field nik @works table
- Remove field calendar_id @works table
- Add table contract_elements and contract_works
5. Structure of route : inside packages, called in bootstrap
6. Readable swagger