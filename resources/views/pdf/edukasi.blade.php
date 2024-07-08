<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <table class="table table-bordered">
    <thead>
      <tr>
        <td><b>Company Name</b></td>
        <td><b>Department Name</b></td>
        <td><b>Team Lead name</b></td>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td>
          {{$show->company_name}}
        </td>
        <td>
          {{$show->department_name}}
        </td>
        <td>
          {{$show->team_lead_name}}
        </td>
      </tr>
      </tbody>
    </table>
  </body>
</html>
