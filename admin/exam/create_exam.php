<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Examination Modal</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f3f4f6;
      margin: 0;
      padding: 0;
    }
    .modal {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 600px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      padding: 24px;
      z-index: 1000;
    }
    .modal h2 {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 24px;
    }
    .form-group {
      margin-bottom: 16px;
    }
    label {
      font-size: 12px;
      display: block;
      margin-bottom: 4px;
      color: #6b7280;
    }
    input[type="text"],
    input[type="number"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
    }
    textarea {
      resize: vertical;
      height: 100px;
    }
    .actions {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      margin-top: 16px;
    }
    button {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
    }
    .cancel-btn {
      background-color: #e5e7eb;
      color: #111827;
    }
    .next-btn {
      background-color: #2563eb;
      color: white;
    }
    .close {
      position: absolute;
      top: 16px;
      right: 16px;
      cursor: pointer;
      font-size: 18px;
    }
  </style>
</head>
<body>

<div class="modal">
  <div class="close">&times;</div>
  <h2>Create New Examination</h2>
  <form>
    <div class="form-group">
      <label>Examination Title</label>
      <input type="text" value="TSD">
    </div>

    <div class="form-group" style="display: flex; gap: 16px;">
      <div style="flex: 1;">
        <label>Position</label>
        <select>
          <option>Data Analyst</option>
        </select>
      </div>
      <div style="flex: 1;">
        <label>Duration (minutes)</label>
        <input type="number" value="60">
      </div>
    </div>

    <div class="form-group">
      <label>Description</label>
      <textarea>TSD</textarea>
    </div>

    <div class="form-group" style="display: flex; gap: 16px;">
      <div style="flex: 1;">
        <label>Passing Score (%)</label>
        <input type="number" value="75">
      </div>
      <div style="flex: 1;">
        <label>Status</label>
        <select>
          <option>Active</option>
          <option>Inactive</option>
        </select>
      </div>
    </div>

    <div class="actions">
      <button type="button" class="cancel-btn">Cancel</button>
      <button type="submit" class="next-btn">Next</button>
    </div>
  </form>
</div>

</body>
</html>
