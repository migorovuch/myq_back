# Add environment variables to AWS Parameter Store on the same path prefix for the same project
# To get the environment variables for a project, get the variables by path
# Convert it to a .env file format
# env1=abc
# env2=def
# separated by the newline character
# and write to a .env file

myService=/myq/prod
# Get variables from SSM and chop off the service name from the variable names (/myservice_name/stage_name/PORT to PORT) and write to a JSON file
# (env.json)
# [   {
#         "Name": "RANDOM_NUMBER",
#         "Value": "99"
#     },
#     {
#         "Name": "RANDOM_SECRET",
#         "Value": "324546JYHBERGYTUDHGsANEFRGW5cerhuwdfshd"
#     }
# ]
aws ssm get-parameters-by-path --with-decryption --path $myService --query "Parameters[*].{Name:Name,Value:Value}" | sed 's,'"$myService/"',,' > env.json


# Convert JSON array to variable statements using jq (https://stedolan.github.io/jq/) command line JSON utility tool
# (.env)
# RANDOM_NUMBER=99
# RANDOM_SECRET=324546JYHBERGYTUDHGsANEFRGW5cerhuwdfshd
jq -jr '.[]|.Name,"=",.Value,"\n"' < env.json >> .env.local
rm env.json