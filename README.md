## Unauthenticated requests
#### tasks, get task by id, get tasks sorted by complete or incomplete


| route        | method      | description           |
| -------------|:-----------:| ---------------------:|
| /            | post        | get all tasks         |
| /{id}        | post        | get task by id        |
| /complete    | post        | get task by complete  | 
| /incomplete  | patch       | get task by incomplete|
| /user/login  | delete      | login                 |

## Authenticated requests:
### Required headers:
- 'Content-Type': 'application/json' or 'Content-Type':'x-www-form-urlencoded'
- 'Authorization': 'jwt string' (get jwt by login)



| route        | method      | description |
| -------------|:-----------:| ----------: |
| /user/create | post        | create user |
| /user/delete | delete      | delete user |
| /user/confirm| get         | confirm auth|
| /            | post        | create post | 
| /{id}        | patch       | patch post  |
| /{id}        | delete      | delete post |


#### Task:

| Property     | Type          | User input |
| -------------|:-------------:| ----------:|
| title (required)        | string        | string     |
| description  | string        | string     |
| deadline     | datetime      | string: 'm-d-Y', 'm-d-Y H:i:s', 'Y-m-d', 'Y-m-d H:i:s'| 
| completed    | string        | 'Y' or 'N' ('N' is default)|

#### User:

| Property           | Type        | User input |
| -------------------|:-----------:| ----------:|
| username (required)| string      | string     |
| password (required)| string      | string     |
