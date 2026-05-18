<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-info-circle"></i> Log Details</h4>
            <a href="index.php?page=audit" class="btn btn-secondary float-end">Back to Logs</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 35%;">Log ID</th>
                            <td><?php echo $log['id']; ?></td
                        </tr>
                        <tr>
                            <th>User</th>
                            <td><?php echo htmlspecialchars($log['username']); ?> (<?php echo $log['user_role']; ?>)</td
                        </tr>
                        <tr>
                            <th>Action Type</th>
                            <td>
                                <span class="badge bg-primary"><?php echo ucfirst($log['action_type']); ?></span>
                            </td
                        </tr>
                        <tr>
                            <th>Action</th>
                            <td><?php echo htmlspecialchars($log['action']); ?></td
                        </tr>
                        <tr>
                            <th>Target Type</th>
                            <td><?php echo ucfirst($log['target_type'] ?? 'N/A'); ?></td
                        </tr>
                        <tr>
                            <th>Target ID</th>
                            <td><?php echo $log['target_id'] ?? 'N/A'; ?></td
                        </tr>
                        <tr>
                            <th>Target Name</th>
                            <td><?php echo htmlspecialchars($log['target_name'] ?? 'N/A'); ?></td
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td><?php echo $log['ip_address']; ?></td
                        </tr>
                        <tr>
                            <th>User Agent</th>
                            <td><small><?php echo htmlspecialchars($log['user_agent']); ?></small></td
                        </tr>
                        <tr>
                            <th>Date & Time</th>
                            <td><?php echo date('F d, Y h:i:s A', strtotime($log['created_at'])); ?></td
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Details</h5>
                        </div>
                        <div class="card-body">
                            <pre style="background: #f5f5f5; padding: 15px; border-radius: 5px;"><?php 
                                $details = json_decode($log['details'], true);
                                if ($details) {
                                    print_r($details);
                                } else {
                                    echo htmlspecialchars($log['details'] ?? 'No additional details');
                                }
                            ?></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>