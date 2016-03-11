REPORT Person System Sub System 
1. Prefix : hrps
2. Database .env Configuration : *_HR_PERSON
3. Release Table : persons, person_documents, marital_status, relatives
4. Changes From V1 to V2
- Turn Person documents as json table
- Remove field uniqid @persons table
- Add field on @relatives table
5. Structure of route : inside packages, called in bootstrap
6. Readable swagger