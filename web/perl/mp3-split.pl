#!/usr/bin/perl -w

# Perl script to split an MP3 file into 15-second chunks, with random file names,
# and also a file containing SQL statements appropriate to insert the chunks into a
# SongID database.

# Usage: mp3-split.pl [input file] [description] [output-dir] [output-url-dir]

# These modules will need to be installed using CPAN
use MP3::Info;
use MP3::Splitter;
use Crypt::Random::Source;

# These modules should come by default with Perl.
use MIME::Base64;
use POSIX qw(ceil);
use File::Basename;
use File::Spec::Functions;

use strict;

my $chunk_length = 15;

sub usage() {
	die "Usage: $0 input-file.mp3 description output-dir output-url-dir\n"
}

usage() if (scalar(@ARGV) != 4);

my ($input_file, $desc, $output_dir, $output_url) = @ARGV;

(my $quoted_desc = $desc) =~ s/([\\"'\0\n\r\cZ])/\\$1/g;

my $input_file_info = get_mp3info($input_file) or die("Unable to read $input_file: $@");

my $input_file_length = $input_file_info->{SECS} + $input_file_info->{MS} / 1000;

my $num_chunks = ceil($input_file_length / $chunk_length);

my $filebase = fileparse($input_file, ".mp3");

print STDERR "$filebase: total length $input_file_length secs, $num_chunks chunks\n";

my $sqlfile = catfile($output_dir, "$filebase.sql");

open (my $sql, ">", $sqlfile) or die ("Unable to open $sqlfile: $!");

sub name_callback($$$$$) {
	my ($pieceNum, $mp3name, $piece, $Xing, $opts) = @_;

	my $randbytes = Crypt::Random::Source::get_strong(32);
	my $mp3_file = catfile($output_dir, MIME::Base64::encode_base64url($randbytes) . ".mp3");

	return $mp3_file;
}

sub after_write($$$$$) {
	my ($mp3name, $piece, $pieceNum, $cur_total_time, $piece_time,
		$piece_name, $cur_total_frames, $piece_frames,
		$xing_written, $Xing, $opts) = @_;

	my $filename = fileparse($piece_name);
	my $url = $output_url . "/" . $filename;

	print $sql "INSERT INTO `CLIPS` (`url`, `description`) VALUES('$url', '$quoted_desc: clip $pieceNum of $num_chunks');\n";
}

my @splitter_opts;

# There's probably a cleverer Perl way to do this
for (my $i = 0; $i < $num_chunks; $i++) {
	push(@splitter_opts, [">0", 15]);
}

mp3split($input_file,
		 {lax => 15, name_callback => \&name_callback, after_write => \&after_write},
		 @splitter_opts);

close($sql) or die("Couldn't close $sqlfile: $!");
