swagger: '2.0'
info:
  title: Public API
  description: Move your app forward with the Public API
  version: 1.0.0
host: localhost:8800
schemes:
  - https
basePath: /v1
produces:
  - application/json
paths:
  /customer/sign/in:
    post:
      summary: Customer sign in
      description: |
        Allow new customer signed in.
      parameters:
        - name: access_token
          in: query
          description: Access token given by apps.
          required: true
          type: string
          format: string
        - name: email
          in: query
          description: customer email
          required: true
          type: string
          format: string
        - name: password
          in: query
          description: password of user.
          required: true
          type: string
      tags:
        - Account
        - Sign
        - In
      responses:
        '200':
          description: An array of user's data
          schema:
            type: array
            items:
              $ref: '#/definitions/user'
        default:
          description: Unexpected error
          schema:
            $ref: '#/definitions/Error'
  /customer/sign/up:
    post:
      summary: Register new customer 
      description: >
        Register new customer
      parameters:
        - name: access_token
          in: query
          description: Access token given by apps.
          required: true
          type: string
          format: string
        - name: name
          in: query
          description: max 255 char.
          required: true
          type: string
          format: string
        - name: email
          in: query
          description: Must be unique.
          required: true
          type: string
          format: string
        - name: password
          in: query
          description: minimum 8 char.
          required: true
          type: string
          format: string
        - name: sso_id
          in: query
          description: let it null for manuall register (not required).
          required: false
          type: string
          format: string
        - name: sso_media
          in: query
          description: only available for facebook.
          required: false
          type: string
          format: string
        - name: sso_data
          in: query
          description: must be json of array sso data.
          required: false
          type: string
          format: string
        - name: gender
          in: query
          description: must be one of femail or male.
          required: false
          type: string
          format: string
        - name: date_of_birth
          in: query
          description: must be in format Y-m-d H:i:s.
          required: false
          type: string
          format: string
      tags:
        - Account
        - Sign
        - Up
      responses:
        '200':
          description: An array of user
          schema:
            type: array
            items:
              $ref: '#/definitions/user'
        default:
          description: Unexpected error
          schema:
            $ref: '#/definitions/Error'
  /customer/activate:
    post:
      summary: activate customers' account
      description: 'Activate account and claim gift.'
      parameters:
        - name: access_token
          in: query
          description: Access token given by apps.
          required: true
          type: number
          format: integer
        - name: link
          in: query
          description: activation code send to mail.
          required: true
          type: string
          format: string
      tags:
        - Account
        - Activate
      responses:
        '200':
          description: user rich data
          schema:
            type: array
            items:
              $ref: '#/definitions/user'
        default:
          description: Unexpected error
          schema:
            $ref: '#/definitions/Error'
definitions:
  user:
    properties:
      id:
        type: number
      name:
        type: string
      email:
        type: string
      date_of_birth:
        type: string
      role:
        type: string
      gender:
        type: string
  Error:
    type: object
    properties:
      status:
        type: string
      data:
        type: string
      message:
        type: string
      code:
        type: integer
        format: int32