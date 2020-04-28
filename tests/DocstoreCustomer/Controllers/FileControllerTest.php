<?php

namespace Tests\DocstoreCustomer\Controllers;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use D2EM, Storage;

use Entities\User as UserEntity;

use Illuminate\Foundation\Testing\WithoutMiddleware;

use Illuminate\Http\UploadedFile;

use IXP\Models\DocstoreCustomerFile;

use Tests\TestCase;

class FileControllerTest extends TestCase
{

    const testInfo = [
        'custuser'              => 'hecustuser',
        'custadmin'             => 'hecustadmin',
        'custadminImagine'      => 'imcustadmin',
        'superuser'             => 'travis',
        'folderName'            => 'Folder 3',
        'folderDescription'     => 'This is the folder 3',
        'disk'                  => 'docstore_customers',
        'customerId'            => 5,
        'fileName'              => 'File2.pdf',
        'fileDescription'       => 'This is file2.pdf',
        'filePrivs'             => UserEntity::AUTH_SUPERUSER,
        'parentDirId'           => null,
        'fileName2'             => 'File3.pdf',
        'fileDescription2'      => 'This is file3.pdf',
        'filePrivs2'            => UserEntity::AUTH_CUSTADMIN,
        'parentDirId2'          => 5,
        'fileName3'             => 'File4.txt',
        'fileDescription3'      => 'This is file4.txt',
        'textFile'              => 'I am the file4.txt',
        'filePrivs3'            => UserEntity::AUTH_CUSTADMIN,
        'parentDirId3'          => 5,
    ];

    /**
     * Test store an object for a superuser
     *
     * @return void
     */
    public function testStoreSuperUser2()
    {
        // test Superuser
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );
        $this->actingAs( $user )->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] );
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test the access to the upload form for a public user
     *
     * @return void
     */
    public function testUploadFormAccessPublicUser()
    {


        // public user
        $response = $this->get( route( 'docstore-c-file@upload' , [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
    }

    /**
     * Test the access to the upload form for a cust user
     *
     * @return void
     */
    public function testUploadFormAccessCustUser()
    {
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-c-file@upload', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the upload form for a cust admin
     *
     * @return void
     */
    public function testUploadFormAccessCustAdmin()
    {
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-c-file@upload', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the upload form for a super user
     *
     * @return void
     */
    public function testUploadFormAccessSuperUser()
    {
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-c-file@upload', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(200 );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStorePublicUser()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $response = $this->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ]  ), [
            'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ],
            'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
        ] );

        $response->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStoreCustUser()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $response = $this->actingAs( $user )->post( route( 'docstore-c-file@store' , [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
            'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ],
            'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
        ] );

        $response->assertStatus(403 );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStoreCustAdmin()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );

        $response = $this->actingAs( $user )->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ]  ), [
            'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ],
            'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
        ] );

        $response->assertStatus(403 );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ] , 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a super user
     *
     * @return void
     */
    public function testStoreSuperUser()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-c-file@store' , [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
            'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ],
            'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ], 'created_by' => $user->getId()
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
    }

    /**
     * Test store an object with no name
     *
     * @return void
     */
    public function testStoreWithoutName()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
            'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );
    }

    /**
     * Test store an object with no file
     *
     * @return void
     */
    public function testStoreWithoutFile()
    {
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
            'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ],
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );
    }

    /**
     * Test store an object with a bad sha256
     *
     * @return void
     */
    public function testStoreWithWrongSha256()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
            'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ], 'uploadedFile'  => $uploadedFile, 'sha256' => '93fc19ea1eb40b8ef8984a7c53dd7b94cb690d5ae5f8b3497c206b43e0bfe117'
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => '93fc19ea1eb40b8ef8984a7c53dd7b94cb690d5ae5f8b3497c206b43e0bfe117',
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );
    }

    /**
     * Test store an object with a wrong min priv
     *
     * @return void
     */
    public function testStoreWithWrongMinPivs()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
            'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => 4, 'uploadedFile'  => $uploadedFile,
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => 4, 'created_by' => $user->getId()
        ] );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testUpdatePublicUser()
    {

        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->put( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a cust user
     *
     * @return void
     */
    public function testUpdateCustUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $this->actingAs( $user )->put( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a cust admin
     *
     * @return void
     */
    public function testUpdateCustAdmin()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );

        $this->actingAs( $user )->put( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
    }

    /**
     * Test to store an object with a post method
     *
     * @return void
     */
    public function testUpdateWithPostMethod()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $response = $this->actingAs( $user )->post( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $response->assertStatus(405 );
    }

    /**
     * Test to store an object for a super user
     *
     * @return void
     */
    public function testUpdateSuperUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName' ] ] )->get()->last();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->put( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ], 'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $file->path );
    }

    /**
     * Test view a none viewable object for a public user
     *
     * @return void
     */
    public function testViewNoneViewableFilePublicUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $this->get( route( 'docstore-c-file@view', [  'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
    }

    /**
     * Test view a none viewable object for a cust user
     *
     * @return void
     */
    public function testViewNoneViewableFileCustUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertStatus(404 );
    }

    /**
     * Test view a none viewable object for a cust admin
     *
     * @return void
     */
    public function testViewNoneViewableFileCustAdmin()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-c-file@view', [  'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertStatus(403 );
    }

    /**
     * Test view a none viewable object for a super user
     *
     * @return void
     */
    public function testViewNoneViewableFileSuperUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-c-file@view', [  'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertRedirect( route( 'docstore-c-file@download' , [  'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) );
    }

    /**
     * Test to download an object for a public user
     *
     * @return void
     */
    public function testDownloadPublicUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->get( route( 'docstore-c-file@download', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) );
        $response->assertStatus( 302 )
            ->assertRedirect( route('login@showForm' ) );
    }

    /**
     * Test to download an object for a cust user
     *
     * @return void
     */
    public function testDownloadCustUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-c-file@download', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) );
        $response->assertStatus( 404 );
    }

    /**
     * Test to download an object for a cust admin
     *
     * @return void
     */
    public function testDownloadCustAdmin()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadminImagine' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-c-file@download', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) );
        $response->assertStatus( 200 );
    }

    /**
     * Test to download an object for a superuser
     *
     * @return void
     */
    public function testDownloadSuperUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-c-file@download', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) );
        $response->assertStatus( 200 );
    }

    /**
     * Test to get info for an object for a public user
     *
     * @return void
     */
    public function testInfoPublicUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->get( route( 'docstore-c-file@info', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) );
        $response->assertStatus( 302 )
            ->assertRedirect( route('login@showForm' ) );
    }

    /**
     * Test to get info for an object for a cust user
     *
     * @return void
     */
    public function testInfoCustUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-c-file@info', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) );
        $response->assertStatus( 404 );
    }

    /**
     * Test to get info for an object for a custadmin
     *
     * @return void
     */
    public function testInfoCustAdmin()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-c-file@info', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) );
        $response->assertStatus( 403 );
    }

    /**
     * Test to get info for an object for a superuser
     *
     * @return void
     */
    public function testInfoSuperUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-c-file@info', [ 'cust' => self::testInfo[ 'customerId' ] ,'file' => $file ] ) );
        $response->assertStatus( 200 )
            ->assertViewIs('docstore-customer.file.info' );
    }

    /**
     * Test delete an object for a public user
     *
     * @return void
     */
    public function testDeletePublicUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $this->delete( route( 'docstore-c-file@delete', [  'cust' => self::testInfo[ 'customerId' ] ,'file' => $file ] ) )
            ->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );
    }

    /**
     * Test delete an object for a cust user
     *
     * @return void
     */
    public function testDeleteCustUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $this->actingAs( $user )->delete( route( 'docstore-c-file@delete', [  'cust' => self::testInfo[ 'customerId' ] ,'file' => $file ] ) )
            ->assertStatus(404 );

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => 1
        ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteCustAdmin()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );
        $this->actingAs( $user )->delete( route( 'docstore-c-file@delete', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => 1
        ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteSuperUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )
            ->delete( route( 'docstore-c-file@delete', [  'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) );
        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );
    }

    /**
     * Store a viewable object
     *
     * @return void
     */
    public function testStoreViewableObject()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName3' ], self::testInfo[ 'textFile' ] );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-c-file@store', [  'cust' => self::testInfo[ 'customerId' ] ] ), [
            'name' =>  self::testInfo[ 'fileName3' ], 'description' => self::testInfo[ 'fileDescription3' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId3' ],
            'min_privs' => self::testInfo[ 'filePrivs3' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId3' ], 'name' =>  self::testInfo[ 'fileName3' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription3' ], 'min_privs' => self::testInfo[ 'filePrivs3' ], 'created_by' => $user->getId()
        ] );

        Storage::disk(self::testInfo[ 'disk' ] )->assertExists(  self::testInfo[ 'customerId' ] . '/' .  $uploadedFile->hashName() );
    }

    /**
     * Test view a none viewable object for a public user
     *
     * @return void
     */
    public function testViewPublicUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $this->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertStatus(302 );
    }

    /**
     * Test view a none viewable object for a cust user
     *
     * @return void
     */
    public function testViewCustUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertStatus(404 );
    }

    /**
     * Test view a none viewable object for a cust admin
     *
     * @return void
     */
    public function testViewCustAdmin()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadminImagine' ] ] );
        $this->actingAs( $user )->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertStatus(200 )
            ->assertViewIs( 'docstore-customer.file.view' );
    }

    /**
     * Test view a none viewable object for a super user
     *
     * @return void
     */
    public function testViewSuperUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertStatus(200 )
            ->assertViewIs( 'docstore-customer.file.view' );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDelete2SuperUser()
    {
        $file = DocstoreCustomerFile::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )
            ->delete( route( 'docstore-c-file@delete', [ 'file' => $file ] ) );
        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId3' ], 'name' =>  self::testInfo[ 'fileName3' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription3' ], 'min_privs' => self::testInfo[ 'filePrivs3' ], 'created_by' => $user->getId()
        ] );
    }
}