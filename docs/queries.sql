/*
We probably shouldn't use stored procedures because they are known to be 
bad at scaling (1000+ lines of sql hogging each transaction)

so instead, lets save queries in here for reuse if needed
*/

-- get all barangays that have no assigned