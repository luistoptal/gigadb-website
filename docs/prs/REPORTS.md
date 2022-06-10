# Editorial Manager

Editorial Manager (EM) is the web application used by GigaScience Press to
manage editorial processes for their journals' manuscript submissions.

## Access

1. Go to the Gitlab [cnhk-infra](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd)
project page and make a note of the `editorial_manager_username` and `editorial_manager_password`
login credentials which are required to access the EM website.
2. Open a browser window and go to http://giga.edmgr.com.
3. Input the values for `editorial_manager_username` and `editorial_manager_password` 
into the `Username` and `Password` text fields, and click on the `Editor login`
button.
4. You will now see the `Editor in Chief Main Menu` page.

## Create EM report

1. On the `Editor in Chief Main Menu` page, click on the `Reports` link which is
located in the `Administrative Functions` box.
2. You will now be on the `Reports` page. In the `Report Tools` section, click 
on the `Enterprise Analytics Reporting` link to create editorial reports.
3. You will now be on the `Enterprise Analytics Reporting` page. To create a 
report, click on the `Create Report` link which is located in the left-hand 
column.
4. You should now be presented with a webpage that has an MS Office-like user
interface. It contains several tabs of which the `Data Sources` tab will be
currently displayed. This tab allows you to select a table to query. To combine 
data from two or more tables, you will need to select multiple tables. For 
example, selecting the `Authors` and `Documents` tables will allow you to query
submitted manuscripts and their authors.
5. Once you have selected your tables, click on the `Continue to Fields` button. 
You then need to select the fields that you want to appear in your report. 
The tool will automatically join data from multiple tables. To query
submitted manuscripts and their authors, select the fields: `Author's First Name`, 
`Author's Last Name`, `Article Title`, `Manuscript Number` and `Initial Date Submitted`.
6. Sorting the returned records is also done on the `Fields` tab. Each field has
a gear icon which, if clicked, will display a pop up window. Checking the `Sort (z-a)`
checkbox will enable sorting for a particular field.
7. The `Style` tab can be used to configure the format of delimited file.
8. The `Filters` tab allows you to limit the number of records returned, e.g. by
previous month, or yesterday. This could be used to keep data in the peer review
system up to date with data on a daily basis.

## Publish reports

There is an SFTP icon in the bar underneath the tabs. Clicking on this icon will
display a pop-up window which you can use to enter the SFTP details of the 
server that the report will be sent to. The configuration details below are
required:
```
SFTP Server Address: sftp://gigadb-sftp.rija.dev
Username: <Available in Gitlab cnhk-infra project>
Password: <Available in Gitlab cnhk-infra project>
Subdirectory: editorialmanager 
Email Address: tech_AT_gigasciencejournal.com
```