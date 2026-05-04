## ADDED Requirements

### Requirement: User list page has community dropdown filter
The admin user list page SHALL display a community dropdown selector in the search form area. The dropdown SHALL load all communities from the existing `/community.community/lists` API and display community names as options.

#### Scenario: Community dropdown loads on page mount
- **WHEN** admin user opens the user list page
- **THEN** the community dropdown SHALL be populated with all available communities from the API

#### Scenario: Select a community filters user list
- **WHEN** admin selects a specific community from the dropdown
- **THEN** the user list SHALL be filtered to show only users whose `community_id` matches the selected community
- **AND** the total count SHALL reflect the filtered user count for that community

#### Scenario: Clear community selection restores full list
- **WHEN** admin clears the community dropdown selection
- **THEN** the user list SHALL return to showing all users without community filter

### Requirement: Backend UserLists supports community_id filtering
The backend `UserLists` class SHALL accept a `community_id` parameter. When provided, the query SHALL filter users directly by the `community_id` field on the `user` table.

#### Scenario: Query without community_id returns all users
- **WHEN** UserLists query is executed without community_id parameter
- **THEN** the query SHALL return all users without applying community filter

#### Scenario: Query with community_id returns filtered users
- **WHEN** UserLists query is executed with community_id parameter
- **THEN** the query SHALL filter by `user.community_id = <value>`
- **AND** return only users associated with that community

### Requirement: User model has community_id search scope
The `User` model SHALL include a `searchCommunityIdAttr` search scope that filters by the `community_id` field.

#### Scenario: Search scope applies community filter
- **WHEN** community_id parameter is passed to User model search
- **THEN** the query SHALL include a WHERE condition on `community_id` matching the provided value
