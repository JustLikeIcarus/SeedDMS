INSERT INTO tblWorkflowActions (name) VALUES ('revise');
SET @action_revise = LAST_INSERT_ID();
INSERT INTO tblWorkflowActions (name) VALUES ('approve');
SET @action_approve = LAST_INSERT_ID();
INSERT INTO tblWorkflowActions (name) VALUES ('complete');
SET @action_complete = LAST_INSERT_ID();
INSERT INTO tblWorkflowActions (name) VALUES ('review');
SET @action_review = LAST_INSERT_ID();
INSERT INTO tblWorkflowActions (name) VALUES ('reject');
SET @action_reject = LAST_INSERT_ID();

INSERT INTO tblWorkflowStates (name, visibility) VALUES ('UNDERREVISION', 0);
SET @state_u = LAST_INSERT_ID();
INSERT INTO tblWorkflowStates (name, visibility) VALUES ('WAITING_FOR_QM', 0);
SET @state_w = LAST_INSERT_ID();
INSERT INTO tblWorkflowStates (name, visibility) VALUES ('APPROVED', 1);
SET @state_a = LAST_INSERT_ID();
INSERT INTO tblWorkflowStates (name, visibility) VALUES ('REJECTED', 0);
SET @state_r = LAST_INSERT_ID();

INSERT INTO tblWorkflows (name, initstate) VALUES ('Standard', @state_u);
SET @workflowid = LAST_INSERT_ID();

INSERT INTO tblWorkflowTransitions (workflow, state, action, nextstate) VALUES (@workflowid, @state_u, @action_complete, @state_w);
INSERT INTO tblWorkflowTransitions (workflow, state, action, nextstate) VALUES (@workflowid, @state_w, @action_approve, @state_a);
INSERT INTO tblWorkflowTransitions (workflow, state, action, nextstate) VALUES (@workflowid, @state_w, @action_reject, @state_r);
INSERT INTO tblWorkflowTransitions (workflow, state, action, nextstate) VALUES (@workflowid, @state_w, @action_revise, @state_u);
INSERT INTO tblWorkflowTransitions (workflow, state, action, nextstate) VALUES (@workflowid, @state_a, @action_revise, @state_u);

